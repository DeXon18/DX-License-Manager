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
    protected $parserService;
    protected $auditService;

    public function __construct(
        NXSuiteService $nxService,
        \App\Services\Audit\LicenseParserService $parserService,
        \App\Services\AI\AuditService $auditService
    ) {
        $this->nxService = $nxService;
        $this->parserService = $parserService;
        $this->auditService = $auditService;
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
            'license_file' => 'required|file|max:10240|mimetypes:text/plain,application/octet-stream',
            'motor'        => 'required|in:legacy,salt',
        ]);

        $file    = $request->file('license_file');
        
        // Validación extra de extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['lic', 'txt'])) {
            return back()->withErrors(['license_file' => 'Solo se permiten archivos .lic o .txt.']);
        }

        $content = file_get_contents($file->getRealPath());
        $motor   = $request->input('motor');

        // Extraer metadatos para nomenclatura y almacenamiento
        $metadata = $this->nxService->extractMetadata($content);

        // --- INICIO AUDITORÍA IA ---
        // 1. Limpiar contenido para la IA (ahorro de tokens)
        $cleanContent = $this->parserService->clean($content);
        $detectedHostIds = $this->parserService->detectHostIds($content);

        // 2. Solicitar auditoría asíncrona
        $audit = $this->auditService->requestAudit(
            auth()->id(), 
            $cleanContent,
            $detectedHostIds
        );
        // --- FIN AUDITORÍA IA ---
        
        // Transformar contenido para el usuario
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
            
            $filename = $finalFilename;
        }

        // Si es una petición AJAX, podemos devolver el UUID de la auditoría
        if ($request->ajax()) {
            return response()->json([
                'uuid' => $audit->uuid,
                'status' => 'processing'
            ]);
        }

        // Devolver para descarga inmediata
        return response($transformedContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
