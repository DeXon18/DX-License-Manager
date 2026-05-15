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
            'license_file' => 'required|file|max:10240',
            'motor'        => 'required|in:legacy,salt',
        ]);

        $file    = $request->file('license_file');
        
        // Validación extra de extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['lic', 'txt'])) {
            return back()->withErrors(['license_file' => 'Solo se permiten archivos .lic o .txt.']);
        }

        try {
            $content = file_get_contents($file->getRealPath());
            $motor   = $request->input('motor');

            // Extraer metadatos para nomenclatura y almacenamiento
            $metadata = $this->nxService->extractMetadata($content);
            $isTemporal = ($metadata['type'] === 'Temporal');

            // --- INICIO AUDITORÍA IA ---
            try {
                $cleanContent = $this->parserService->clean($content);
                $detectedHostIds = $this->parserService->detectHostIds($content);

                $audit = $this->auditService->requestAudit(
                    auth()->id(), 
                    $cleanContent,
                    $detectedHostIds,
                    'siemens',
                    $isTemporal
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("NXSuite: IA Audit failed but continuing process: " . $e->getMessage());
                $audit = null;
            }
            // --- FIN AUDITORÍA IA ---
            
            // Transformar contenido para el usuario
            $transformedContent = $this->nxService->transform($content, $motor, $isTemporal);

            // Generar nombre de archivo
            $filename = $this->nxService->generateFilename($metadata);

            // Lógica de Almacenamiento (Solo si NO es temporal)
            if ($metadata['type'] !== 'Temporal') {
                try {
                    $clientFolder = $this->normalizationService->normalizeName($metadata['client']);
                    $dateFolder = date('m-Y');
                    
                    $storagePath = "licenses/siemens/{$clientFolder}/{$dateFolder}";
                    $finalFilename = $filename;
                    
                    // Manejo de duplicados
                    $counter = 1;
                    $nameOnly = str_replace(['.lic', '.txt'], '', $filename);
                    $ext = str_contains($filename, '.txt') ? '.txt' : '.lic';

                    while (Storage::disk('local')->exists("{$storagePath}/{$finalFilename}")) {
                        $finalFilename = "{$nameOnly}_{$counter}{$ext}";
                        $counter++;
                    }
                    
                    Storage::disk('local')->put("{$storagePath}/{$finalFilename}", $transformedContent);
                    $filename = $finalFilename;
                    
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("NXSuite: Storage failed: " . $e->getMessage());
                    // Continuamos para permitir al menos la descarga
                }
            }

            // Si es una petición que espera JSON (AJAX con cabecera Accept: application/json)
            if ($request->wantsJson()) {
                return response()->json([
                    'uuid' => $audit ? $audit->uuid : null,
                    'status' => $audit ? 'processing' : 'skipped_error',
                    'filename' => $filename
                ]);
            }

            // Devolver para descarga inmediata
            return response($transformedContent)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("NXSuite: Critical failure: " . $e->getMessage());
            return back()->withErrors(['license_file' => 'Error crítico al procesar la licencia. Consulte los logs del sistema.']);
        }
    }
}
