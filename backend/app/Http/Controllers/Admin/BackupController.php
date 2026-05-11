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
            $backups[] = [
                'name' => $file,
                'size' => $this->formatBytes(filesize($path)),
                'date' => date('Y-m-d H:i:s', filemtime($path)),
                'env' => str_starts_with($file, 'prod') ? 'PROD' : 'BETA',
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
