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
     * Handle the CSV upload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:51200|mimes:csv,txt|mimetypes:text/csv,text/plain,application/csv',
        ]);

        try {
            $file = $request->file('csv_file');
            $result = $this->importService->import($file->getRealPath(), $file->getClientOriginalName());

            $message = "Importación completada: {$result['processed']} registros procesados.";
            if (count($result['errors']) > 0 || count($result['warnings']) > 0) {
                $count = count($result['errors']) + count($result['warnings']);
                $message .= " Se han detectado {$count} avisos de integridad/normalización.";
            }

            return redirect()->back()->with([
                'success' => $message,
                'log_id' => $result['log_id']
            ]);

        } catch (\Exception $e) {
            Log::error('CSV Import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error en la importación. Contacte con el administrador.');
        }
    }
}
