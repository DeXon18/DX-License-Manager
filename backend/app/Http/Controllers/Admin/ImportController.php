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
            $fullPath = storage_path('app/' . $path);

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
}
