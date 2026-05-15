<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Services\Licensing\HeedsService;
use App\Services\Audit\LicenseParserService;
use App\Services\AI\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeedsController extends Controller
{
    protected $heedsService;
    protected $parserService;
    protected $auditService;
    protected $normalizationService;

    public function __construct(
        HeedsService $heedsService,
        LicenseParserService $parserService,
        AuditService $auditService,
        \App\Services\System\StorageNormalizationService $normalizationService
    ) {
        $this->heedsService = $heedsService;
        $this->parserService = $parserService;
        $this->auditService = $auditService;
        $this->normalizationService = $normalizationService;
    }

    /**
     * Muestra la vista de la herramienta.
     */
    public function index()
    {
        return view('tools.heeds');
    }

    /**
     * Procesa la licencia subida.
     */
    public function process(Request $request)
    {
        ini_set('memory_limit', '256M');
        $request->validate([
            'license_file' => 'required|file|max:10240',
        ]);

        $file    = $request->file('license_file');

        // Validación extra de extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['lic', 'txt', 'dat', 'cid'])) {
            return back()->withErrors(['license_file' => 'Solo se permiten archivos .lic, .txt, .dat o .cid.']);
        }

        try {
            $content = file_get_contents($file->getRealPath());

            // 1. Extraer metadatos
            $metadata = $this->heedsService->extractMetadata($content);
            $isTemporal = ($metadata['type'] === 'Temporal');

            // 2. Auditoría IA (Principio Solo Log)
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
                \Illuminate\Support\Facades\Log::warning("HEEDS: IA Audit failed but continuing: " . $e->getMessage());
            }

            // 3. Transformación (SALT 29000)
            $transformedContent = $this->heedsService->transform($content, $isTemporal);

            // 4. Generar nombre de archivo
            $filename = $this->heedsService->generateFilename($metadata);

            // 5. Almacenamiento Jerárquico (si no es temporal)
            if ($metadata['type'] !== 'Temporal') {
                try {
                    $clientFolder = $this->normalizationService->normalizeName($metadata['client']);
                    $dateFolder = date('m-Y');
                    
                    $storagePath = "licenses/siemens/{$clientFolder}/{$dateFolder}";
                    
                    // Manejo de duplicados
                    $counter = 1;
                    $finalFilename = $filename;
                    $nameOnly = str_replace(['.lic', '.txt'], '', $filename);
                    $ext = str_contains($filename, '.txt') ? '.txt' : '.lic';

                    while (Storage::disk('local')->exists("{$storagePath}/{$finalFilename}")) {
                        $finalFilename = "{$nameOnly}_{$counter}{$ext}";
                        $counter++;
                    }
                    
                    Storage::disk('local')->put("{$storagePath}/{$finalFilename}", $transformedContent);
                    $filename = $finalFilename;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("HEEDS: Storage failed: " . $e->getMessage());
                }
            }

            // Devolver para descarga inmediata
            return response($transformedContent)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("HEEDS: Critical failure: " . $e->getMessage());
            return back()->withErrors(['license_file' => 'Error crítico al procesar la licencia HEEDS.']);
        }
    }
}
