<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Data\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(CsvImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show the import form.
     */
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Handle the CSV upload async.
     */
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:51200|mimes:csv,txt|mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel',
        ]);

        try {
            $file = $request->file('csv_file');
            $originalName = $file->getClientOriginalName();
            
            // Store file safely
            $path = $file->storeAs('imports', uniqid() . '_' . $originalName);
            $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($path);

            // Create initial log
            $log = \App\Models\ImportLog::create([
                'filename' => $originalName,
                'status' => 'processing',
            ]);

            // Dispatch job
            \App\Jobs\ProcessCsvImportJob::dispatch($fullPath, $originalName, $log->id);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'log_id' => $log->id,
                    'message' => 'Archivo encolado para procesamiento.'
                ]);
            }

            return redirect()->back()->with('success', 'Archivo enviado a la cola.');

        } catch (\Exception $e) {
            Log::error('CSV Import Queue failed', ['error' => $e->getMessage()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error en la importación. Contacte con el administrador.');
        }
    }

    /**
     * Get live status of an import via Redis.
     */
    public function status($logId)
    {
        $redisKey = "import_console_{$logId}";
        $redisProgressKey = "import_progress_{$logId}";

        $progress = \Illuminate\Support\Facades\Redis::get($redisProgressKey) ?: 0;
        
        // Extract all lines in list
        $lines = \Illuminate\Support\Facades\Redis::lrange($redisKey, 0, -1);
        if (!$lines) {
            $lines = [];
        }

        $log = \App\Models\ImportLog::find($logId);
        
        return response()->json([
            'progress' => (int) $progress,
            'lines' => $lines,
            'status' => $log ? $log->status : 'unknown'
        ]);
    }

    /**
     * Get list of active (processing) imports.
     */
    public function active()
    {
        $activeJobs = \App\Models\ImportLog::where('status', 'processing')
            ->orderBy('id', 'desc')
            ->get();
            
        // For each active job, get current progress from Redis
        $jobsWithProgress = $activeJobs->map(function ($job) {
            $redisProgressKey = "import_progress_{$job->id}";
            $progress = \Illuminate\Support\Facades\Redis::get($redisProgressKey) ?: 0;
            
            return [
                'id' => $job->id,
                'filename' => $job->filename,
                'status' => $job->status,
                'progress' => (int) $progress,
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $job->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'active_jobs' => $jobsWithProgress
        ]);
    }

    /**
     * Cancel an active processing import.
     */
    public function cancel($logId)
    {
        $redisCancelKey = "import_cancel_{$logId}";
        \Illuminate\Support\Facades\Redis::set($redisCancelKey, true);
        \Illuminate\Support\Facades\Redis::expire($redisCancelKey, 3600);

        $log = \App\Models\ImportLog::find($logId);
        if ($log && $log->status === 'processing') {
            $log->update(['status' => 'canceled']);
        }

        return response()->json(['success' => true]);
    }
}
