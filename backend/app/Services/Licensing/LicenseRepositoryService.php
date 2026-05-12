<?php

namespace App\Services\Licensing;

use App\Models\LicenseArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyLicenseReport;

class LicenseRepositoryService
{
    /**
     * Genera el repositorio semanal de licencias.
     */
    public function generateWeeklyArchive(string $origin = 'auto', bool $sendEmail = false): ?LicenseArchive
    {
        // El lunes a las 07:00 archivamos la semana anterior (ISO week)
        $lastWeek = Carbon::now()->subWeek();
        $weekNumber = $lastWeek->format('W');
        $year = $lastWeek->format('Y');
        
        $filename = "REPOSITORIO_SEMANAL_S{$weekNumber}_{$year}.zip";
        $storageDir = "archives";
        $storagePath = "{$storageDir}/{$filename}";

        // Asegurar que el directorio de archivos existe
        if (!Storage::disk('local')->exists($storageDir)) {
            Storage::disk('local')->makeDirectory($storageDir);
        }

        // Buscar archivos procesados la semana pasada
        // Buscamos en licenses/siemens y licenses/moldex3d
        $vendors = ['siemens', 'moldex3d'];
        $filesToArchive = [];
        $clientsSummary = [];

        foreach ($vendors as $vendor) {
            $path = "licenses/{$vendor}";
            if (!Storage::disk('local')->exists($path)) continue;

            $allFiles = Storage::disk('local')->allFiles($path);

            foreach ($allFiles as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                $fileDate = Carbon::createFromTimestamp($lastModified);

                if ($fileDate->isSameWeek($lastWeek)) {
                    $filesToArchive[] = $file;
                    
                    // Extraer cliente del path (licenses/vendor/CLIENTE/fecha/file)
                    $parts = explode('/', $file);
                    if (count($parts) >= 3) {
                        $clientName = $parts[2];
                        if (!isset($clientsSummary[$clientName])) {
                            $clientsSummary[$clientName] = 0;
                        }
                        $clientsSummary[$clientName]++;
                    }
                }
            }
        }

        if (empty($filesToArchive)) {
            return null;
        }

        // Crear el ZIP usando la ruta absoluta del disco
        $zipPath = Storage::disk('local')->path($storagePath);
        
        // Asegurar que el directorio padre existe físicamente y es escribible
        $dir = dirname($zipPath);
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \Exception("No se pudo crear el directorio de archivos en {$dir}");
            }
        }

        $zip = new ZipArchive();
        $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res === TRUE) {
            foreach ($filesToArchive as $file) {
                $absolutePath = Storage::disk('local')->path($file);
                // Mantener estructura relativa dentro del ZIP (quitando licenses/)
                $zipRelativePath = str_replace('licenses/', '', $file);
                $zip->addFile($absolutePath, $zipRelativePath);
            }
            $zip->close();
        } else {
            $errorMap = [
                ZipArchive::ER_EXISTS => 'El archivo ya existe.',
                ZipArchive::ER_INCONS => 'Archivo inconsistente.',
                ZipArchive::ER_INVAL => 'Argumento inválido.',
                ZipArchive::ER_MEMORY => 'Fallo de memoria.',
                ZipArchive::ER_NOENT => 'No existe el archivo.',
                ZipArchive::ER_NOZIP => 'No es un archivo ZIP.',
                ZipArchive::ER_OPEN => 'No se pudo abrir el archivo.',
                ZipArchive::ER_READ => 'Error de lectura.',
                ZipArchive::ER_SEEK => 'Error de búsqueda.',
            ];
            $errorMessage = $errorMap[$res] ?? "Código de error: {$res}";
            throw new \Exception("ZipArchive fallo: {$errorMessage} en la ruta {$zipPath}");
        }

        // Registrar en BD
        return LicenseArchive::updateOrCreate(
            ['filename' => $filename],
            [
                'week_number' => $weekNumber,
                'year' => $year,
                'files_count' => count($filesToArchive),
                'clients_summary' => $clientsSummary,
                'storage_path' => $storagePath,
                'origin' => $origin,
            ]
        );

        // Envío opcional por correo
        if ($sendEmail && $archive) {
            Mail::to('Soporte@ats-global.com')->send(new WeeklyLicenseReport($archive));
        }

        return $archive;

    /**
     * Obtiene el historial de archivos.
     */
    public function getHistory()
    {
        return LicenseArchive::orderBy('year', 'desc')
            ->orderBy('week_number', 'desc')
            ->get();
    }
}
