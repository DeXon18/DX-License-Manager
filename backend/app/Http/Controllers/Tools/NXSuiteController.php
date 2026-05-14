<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\ResourceLink;
use App\Services\Licensing\NXSuiteService;
use App\Services\System\StorageNormalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NXSuiteController extends Controller
{
    protected $nxService;
    protected $parserService;
    protected $auditService;
    protected $normalizationService;

    public function __construct(
        NXSuiteService $nxService,
        \App\Services\Audit\LicenseParserService $parserService,
        \App\Services\AI\AuditService $auditService,
        StorageNormalizationService $normalizationService
    ) {
        $this->nxService = $nxService;
        $this->parserService = $parserService;
        $this->auditService = $auditService;
        $this->normalizationService = $normalizationService;
    }

    public function index()
    {
        return view('tools.nx-suite');
    }

    /**
     * Muestra la página dedicada de recursos Siemens.
     */
    public function resources()
    {
        $resources = ResourceLink::forVendor('siemens')
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        return view('tools.resources', [
            'vendor' => 'siemens',
            'resources' => $resources
        ]);
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
        $isTemporal = ($metadata['type'] === 'Temporal');

        // --- INICIO AUDITORÍA IA ---
        // 1. Limpiar contenido para la IA (ahorro de tokens)
        $cleanContent = $this->parserService->clean($content);
        $detectedHostIds = $this->parserService->detectHostIds($content);

        // 2. Solicitar auditoría asíncrona (Saltará n8n si es temporal)
        $audit = $this->auditService->requestAudit(
            auth()->id(), 
            $cleanContent,
            $detectedHostIds,
            'siemens',
            $isTemporal
        );
        // --- FIN AUDITORÍA IA ---
        
        // Transformar contenido para el usuario
        $transformedContent = $this->nxService->transform($content, $motor, $isTemporal);

        // Generar nombre de archivo
        $filename = $this->nxService->generateFilename($metadata);

        // Lógica de Almacenamiento (Solo si NO es temporal)
        if ($metadata['type'] !== 'Temporal') {
            $clientFolder = $this->normalizationService->normalizeName($metadata['client']);
            $dateFolder = date('m-Y');
            
            // Nueva ruta solicitada: licenses/siemens/{cliente}/{fecha}/
            $storagePath = "licenses/siemens/{$clientFolder}/{$dateFolder}";
            $fullPath = "{$storagePath}/{$filename}";

            // Manejo de duplicados (_1, _2, etc.)
            $counter = 1;
            $finalFilename = $filename;
            $nameOnly = str_replace(['.lic', '.txt'], '', $filename);
            $extension = str_contains($filename, '.txt') ? '.txt' : '.lic';

            while (Storage::disk('local')->exists("{$storagePath}/{$finalFilename}")) {
                $finalFilename = "{$nameOnly}_{$counter}{$extension}";
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
