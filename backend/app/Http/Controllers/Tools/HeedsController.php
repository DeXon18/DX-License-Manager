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

    public function __construct(
        HeedsService $heedsService,
        LicenseParserService $parserService,
        AuditService $auditService
    ) {
        $this->heedsService = $heedsService;
        $this->parserService = $parserService;
        $this->auditService = $auditService;
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
        $request->validate([
            'license_file' => 'required|file',
        ]);

        $file    = $request->file('license_file');
        $content = file_get_contents($file->getRealPath());

        // 1. Extraer metadatos
        $metadata = $this->heedsService->extractMetadata($content);

        // 2. Auditoría IA (Principio Solo Log)
        $cleanContent = $this->parserService->clean($content);
        $detectedHostIds = $this->parserService->detectHostIds($content);

        $audit = $this->auditService->requestAudit(
            auth()->id() ?? 1,
            $cleanContent,
            $detectedHostIds,
            'siemens' // Vendor Siemens para HEEDS
        );

        // 3. Transformación (SALT 29000)
        $transformedContent = $this->heedsService->transform($content);

        // 4. Generar nombre de archivo
        $filename = $this->heedsService->generateFilename($metadata);

        // 5. Almacenamiento Jerárquico (si no es temporal)
        if ($metadata['type'] !== 'Temporal') {
            $clientSlug = Str::slug($metadata['client']);
            $dateFolder = date('m-Y'); // Formato Mes-Año (05-2026)
            
            $storagePath = "licenses/siemens/{$clientSlug}/{$dateFolder}";
            
            // Manejo de duplicados
            $counter = 1;
            $finalFilename = $filename;
            while (Storage::disk('local')->exists("{$storagePath}/{$finalFilename}")) {
                $finalFilename = str_replace('.lic', '', $filename) . "_" . $counter . ".lic";
                $counter++;
            }
            
            Storage::disk('local')->put("{$storagePath}/{$finalFilename}", $transformedContent);
            $filename = $finalFilename;
        }

        // Devolver para descarga inmediata
        return response($transformedContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
