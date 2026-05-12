<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Services\Licensing\StarCcmService;
use App\Services\Audit\LicenseParserService;
use App\Services\AI\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StarCcmController extends Controller
{
    protected $starService;
    protected $parserService;
    protected $auditService;
    protected $normalizationService;

    public function __construct(
        StarCcmService $starService,
        LicenseParserService $parserService,
        AuditService $auditService,
        \App\Services\System\StorageNormalizationService $normalizationService
    ) {
        $this->starService = $starService;
        $this->parserService = $parserService;
        $this->auditService = $auditService;
        $this->normalizationService = $normalizationService;
    }

    /**
     * Muestra la vista de la herramienta.
     */
    public function index()
    {
        return view('tools.star-ccm');
    }

    /**
     * Procesa la licencia subida.
     */
    public function process(Request $request)
    {
        $request->validate([
            'license_file' => 'required|file|max:10240|mimetypes:text/plain,application/octet-stream',
        ]);

        $file    = $request->file('license_file');

        // Validación extra de extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['lic', 'txt'])) {
            return back()->withErrors(['license_file' => 'Solo se permiten archivos .lic o .txt.']);
        }

        $content = file_get_contents($file->getRealPath());

        // 1. Extraer metadatos
        $metadata = $this->starService->extractMetadata($content);

        // 2. Auditoría IA (Principio Solo Log)
        $cleanContent = $this->parserService->clean($content);
        $detectedHostIds = $this->parserService->detectHostIds($content);

        $audit = $this->auditService->requestAudit(
            auth()->id(),
            $cleanContent,
            $detectedHostIds,
            'siemens' // Vendor Siemens para STAR-CCM+
        );

        // 3. Transformación (SALT 29000)
        $transformedContent = $this->starService->transform($content);

        // 4. Generar nombre de archivo
        $filename = $this->starService->generateFilename($metadata);

        // 5. Almacenamiento Jerárquico (si no es temporal)
        if ($metadata['type'] !== 'Temporal') {
            $clientFolder = $this->normalizationService->normalizeName($metadata['client']);
            $dateFolder = date('m-Y'); // Formato Mes-Año (05-2026)
            
            $storagePath = "licenses/siemens/{$clientFolder}/{$dateFolder}";
            
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
