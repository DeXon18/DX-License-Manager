<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Data\CsvImportService;
use Illuminate\Http\Request;

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
            'csv_file' => 'required|file',
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
            return redirect()->back()->with('error', 'Error crítico en la importación: ' . $e->getMessage());
        }
    }
}
