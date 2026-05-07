<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Services\Licensing\NXSuiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NXSuiteController extends Controller
{
    protected $nxService;

    public function __construct(NXSuiteService $nxService)
    {
        $this->nxService = $nxService;
    }

    /**
     * Muestra la vista de la herramienta.
     */
    public function index()
    {
        return view('tools.nx-suite');
    }

    /**
     * Procesa la licencia subida.
     */
    public function process(Request $request)
    {
        $request->validate([
            'license_file' => 'required|file',
            'motor'        => 'required|in:legacy,salt',
        ]);

        $file    = $request->file('license_file');
        $content = file_get_contents($file->getRealPath());
        $motor   = $request->input('motor');

        // Extraer metadatos para nomenclatura y almacenamiento
        $metadata = $this->nxService->extractMetadata($content);
        
        // Transformar contenido
        $isTemporal7Days = ($metadata['type'] === 'Temporal');
        $transformedContent = $this->nxService->transform($content, $motor, $isTemporal7Days);

        // Generar nombre de archivo
        $filename = $this->nxService->generateFilename($metadata);

        // Lógica de Almacenamiento (Solo si NO es temporal)
        if ($metadata['type'] !== 'Temporal') {
            $clientSlug = Str::slug($metadata['client']);
            $dateFolder = date('m-Y');
            
            // Nueva ruta solicitada: licenses/siemens/{cliente}/{fecha}/
            $storagePath = "licenses/siemens/{$clientSlug}/{$dateFolder}";
            $fullPath = "{$storagePath}/{$filename}";

            // Manejo de duplicados (_1, _2, etc.)
            $counter = 1;
            $finalFilename = $filename;
            while (Storage::disk('local')->exists("{$storagePath}/{$finalFilename}")) {
                $finalFilename = $filename . "_" . $counter;
                $counter++;
            }
            
            Storage::disk('local')->put("{$storagePath}/{$finalFilename}", $transformedContent);
            
            // El archivo para descargar sigue siendo el original o el final? 
            // El usuario pidió que el guardado tenga el sufijo. Para la descarga mantendremos el final por consistencia.
            $filename = $finalFilename;
        }

        // Devolver para descarga inmediata
        return response($transformedContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
