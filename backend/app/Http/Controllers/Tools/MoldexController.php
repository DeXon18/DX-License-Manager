<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Services\Licensing\MoldexService;
use App\Services\Licensing\MoldexSyncService;
use App\Services\Audit\MoldexParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MoldexController extends Controller
{
    protected $moldexService;
    protected $parserService;
    protected $syncService;

    public function __construct(
        MoldexService $moldexService,
        MoldexParserService $parserService,
        MoldexSyncService $syncService
    ) {
        $this->moldexService = $moldexService;
        $this->parserService = $parserService;
        $this->syncService = $syncService;
    }

    /**
     * Muestra la vista de la herramienta Moldex3D.
     */
    public function index()
    {
        return view('tools.moldex3d');
    }

    /**
     * Procesa la licencia .mac subida.
     */
    public function process(Request $request)
    {
        $request->validate([
            'license_file' => 'required|file',
        ]);

        $file    = $request->file('license_file');
        $content = file_get_contents($file->getRealPath());

        // 1. Extraer metadatos para nomenclatura y almacenamiento
        $metadata = $this->moldexService->extractMetadata($content);
        $parsedData = $this->parserService->parse($content);

        // 2. Generar nombre de archivo estándar
        $filename = $this->moldexService->generateFilename($metadata);

        // 3. Almacenamiento Estructurado
        $clientSlug = Str::slug($parsedData['customer_name'] ?? 'unknown');
        $year       = $metadata['year'];
        
        $storagePath = "licenses/moldex3d/{$clientSlug}/{$year}";
        $fullPath    = "{$storagePath}/{$filename}";

        // Manejo de duplicados
        $counter = 1;
        $finalFilename = $filename;
        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        while (Storage::disk('local')->exists("private/{$storagePath}/{$finalFilename}")) {
            $finalFilename = "{$nameOnly}_{$counter}.mac";
            $counter++;
        }
        
        // Guardar en disco privado
        Storage::disk('local')->put("private/{$storagePath}/{$finalFilename}", $content);

        // 4. Sincronizar con Inventario Activo
        $parsedData['version'] = $metadata['year'];
        $syncResult = $this->syncService->sync($parsedData);

        // 5. Si es AJAX, devolver JSON con la data para la UI (Bento Grid)
        if ($request->ajax()) {
            return response()->json([
                'success'   => true,
                'metadata'  => $parsedData,
                'filename'  => $finalFilename,
                'path'      => $fullPath,
                'inventory' => $syncResult
            ]);
        }

        // Devolver éxito (la descarga no es necesaria según feedback)
        return back()->with('success', 'Archivo procesado y almacenado correctamente.');
    }
}
