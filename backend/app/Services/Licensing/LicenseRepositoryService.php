<?php

namespace App\Services\Licensing;

use App\Models\LicenseArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Carbon\Carbon;

class LicenseRepositoryService
{
    /**
     * Genera el repositorio semanal de licencias.
     */
    public function generateWeeklyArchive(): ?LicenseArchive
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

        // Crear el ZIP
        $zipPath = storage_path('app/private/' . $storagePath);
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($filesToArchive as $file) {
                $absolutePath = storage_path('app/private/' . $file);
                // Mantener estructura relativa dentro del ZIP (quitando licenses/)
                $zipRelativePath = str_replace('licenses/', '', $file);
                $zip->addFile($absolutePath, $zipRelativePath);
            }
            $zip->close();
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP en {$zipPath}");
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
            ]
        );
    }

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
