<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackups();
        
        // Calcular espacio total ocupado por backups
        $totalSizeBytes = 0;
        foreach ($backups as $b) {
            $totalSizeBytes += $this->rawBytes($b['name']);
        }
        
        $totalSize = $this->formatBytes($totalSizeBytes);

        return view('admin.backups.index', [
            'backups' => $backups,
            'totalSize' => $totalSize,
            'backupDir' => storage_path('app/backups/db')
        ]);
    }

    public function backup()
    {
        try {
            // Ejecutar script de backup con etiqueta manual
            $process = \Illuminate\Support\Facades\Process::run('bash /var/www/html/scripts/backup-db.sh beta manual');
            
            if ($process->successful()) {
                $this->logAction('db_backup', 'Manual database backup created via Backups module');
                return response()->json(['success' => true, 'message' => 'Copia de seguridad generada correctamente.']);
            }

            return response()->json(['success' => false, 'message' => 'Error en el script: ' . $process->errorOutput()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function download($filename)
    {
        if (str_contains($filename, '..') || str_contains($filename, '/')) {
            abort(403, 'Acceso denegado.');
        }

        $path = storage_path("app/backups/db/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        $this->logAction('db_download', "Backup downloaded: {$filename}");
        return response()->download($path);
    }

    public function destroy($filename)
    {
        if (str_contains($filename, '..') || str_contains($filename, '/')) {
            return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $path = storage_path("app/backups/db/{$filename}");

        if (file_exists($path)) {
            unlink($path);
            $this->logAction('db_delete', "Backup deleted: {$filename}");
            return response()->json(['success' => true, 'message' => 'Copia de seguridad eliminada con éxito.']);
        }

        return response()->json(['success' => false, 'message' => 'Archivo no encontrado.'], 404);
    }

    public function restore($filename)
    {
        try {
            if (str_contains($filename, '..') || str_contains($filename, '/')) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado.'], 403);
            }

            $path = storage_path("app/backups/db/{$filename}");

            if (!file_exists($path)) {
                return response()->json(['success' => false, 'message' => 'Archivo no encontrado.'], 404);
            }

            // IMPORTANTE: Se asumen variables de entorno del contenedor y se desactiva SSL interno
            $cmd = sprintf(
                'mariadb --skip-ssl -h %s -u %s -p%s %s < %s',
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($path)
            );

            // Ejecutar via shell_exec o Process
            // Nota: mariadb-client debe estar instalado en el contenedor (ya lo está por Phase 10.4)
            $output = [];
            $resultCode = null;
            exec($cmd . ' 2>&1', $output, $resultCode);

            if ($resultCode === 0) {
                $this->logAction('db_restore', "Database restored from backup: {$filename}");
                return response()->json(['success' => true, 'message' => 'Base de datos restaurada con éxito.']);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Error en la restauración: ' . implode("\n", $output)
            ], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function logAction($action, $description)
    {
        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'level' => 'critical', // Restauraciones y backups son acciones críticas
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }

    private function getBackups()
    {
        $backupDir = storage_path('app/backups/db');
        if (!file_exists($backupDir)) {
            return [];
        }

        $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitignore') continue;

            $path = $backupDir . '/' . $file;
            $parts = explode('_', $file);
            $type = 'MANUAL'; // Default para archivos antiguos
            
            if (count($parts) >= 3) {
                if ($parts[1] === 'system') {
                    $type = 'SISTEMA';
                } elseif ($parts[1] === 'manual') {
                    $type = 'MANUAL';
                }
            }

            $backups[] = [
                'name' => $file,
                'size' => $this->formatBytes(filesize($path)),
                'date' => date('Y-m-d H:i:s', filemtime($path)),
                'env' => str_starts_with($file, 'prod') ? 'PROD' : 'BETA',
                'type' => $type
            ];
        }

        // Ordenar por fecha descendente
        usort($backups, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $backups; // Mostrar todos en esta vista dedicada
    }

    private function rawBytes($filename)
    {
        $path = storage_path("app/backups/db/{$filename}");
        return file_exists($path) ? filesize($path) : 0;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
