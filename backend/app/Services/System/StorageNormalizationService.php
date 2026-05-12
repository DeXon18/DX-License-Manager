<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageNormalizationService
{
    /**
     * Normaliza el nombre del cliente para su uso en rutas de carpetas.
     * Convierte a MAYÚSCULAS y reemplaza guiones por espacios.
     * Elimina puntos y comas (ej. S.L. -> SL).
     */
    public function normalizeName(string $name): string
    {
        // 1. Eliminar puntos y comas directamente
        $name = str_replace(['.', ','], '', $name);

        // 2. Reemplazar guiones y otros caracteres por espacios
        $name = preg_replace('/[\-_]+/', ' ', $name);

        // 3. Pasar a MAYÚSCULAS y limpiar espacios extra
        return Str::upper(trim(preg_replace('/\s+/', ' ', $name)));
    }

    /**
     * Migra las carpetas existentes en el storage de licencias.
     */
    public function migrateExistingFolders(): array
    {
        $vendors = ['siemens', 'moldex3d'];
        $results = [
            'migrated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        foreach ($vendors as $vendor) {
            $path = "licenses/{$vendor}";
            
            if (!Storage::disk('local')->exists($path)) {
                continue;
            }

            $directories = Storage::disk('local')->directories($path);

            foreach ($directories as $dir) {
                $oldName = basename($dir);
                $newName = $this->normalizeName($oldName);

                if ($oldName !== $newName) {
                    $oldPath = $dir;
                    $newPath = "licenses/{$vendor}/{$newName}";

                    try {
                        // Si la carpeta destino ya existe, movemos el contenido
                        if (Storage::disk('local')->exists($newPath)) {
                            $this->moveContents($oldPath, $newPath);
                            Storage::disk('local')->deleteDirectory($oldPath);
                        } else {
                            // Si no existe, renombramos directamente
                            Storage::disk('local')->move($oldPath, $newPath);
                        }
                        $results['migrated']++;
                    } catch (\Exception $e) {
                        $results['errors'][] = "Error migrating {$oldPath} to {$newPath}: " . $e->getMessage();
                    }
                } else {
                    $results['skipped']++;
                }
            }
        }

        return $results;
    }

    /**
     * Mueve el contenido de una carpeta a otra recursivamente.
     */
    private function moveContents(string $source, string $destination)
    {
        $files = Storage::disk('local')->allFiles($source);
        foreach ($files as $file) {
            $relativePath = str_replace($source . '/', '', $file);
            Storage::disk('local')->move($file, $destination . '/' . $relativePath);
        }
    }
}
