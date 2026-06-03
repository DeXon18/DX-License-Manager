<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DockerMonitorService
{
    /**
     * Get the list of project containers with their health and resource usage.
     * Filters by environment (beta/prod).
     */
    public function getContainers()
    {
        $env = config('app.env') === 'production' ? 'prod' : 'beta';
        $prefix = "dx-";
        $suffix = "-{$env}";

        $containers = [];

        try {
            // 1. Get basic info: Name, Status, Image
            $psOutput = shell_exec("docker ps -a --format '{{.Names}}|{{.Status}}|{{.Image}}'");
            if (!$psOutput) return [];

            $psLines = explode("\n", trim($psOutput));
            $statsOutput = shell_exec("docker stats --no-stream --format '{{.Name}}|{{.CPUPerc}}|{{.MemUsage}}|{{.MemPerc}}'");
            $statsMap = $this->parseStats($statsOutput);

            foreach ($psLines as $line) {
                if (empty($line)) continue;

                [$name, $status, $image] = explode('|', $line);

                // Filter by project prefix and environment suffix
                if (str_starts_with($name, $prefix) && str_ends_with($name, $suffix)) {
                    $stats = $statsMap[$name] ?? [
                        'cpu' => '0.00%',
                        'mem_usage' => '0B / 0B',
                        'mem_perc' => '0.00%'
                    ];

                    $containers[] = [
                        'name' => $name,
                        'service' => str_replace([$prefix, $suffix], '', $name),
                        'status' => $this->parseStatus($status),
                        'status_raw' => $status,
                        'image' => $image,
                        'cpu' => $stats['cpu'],
                        'mem_usage' => $stats['mem_usage'],
                        'mem_perc' => $stats['mem_perc'],
                        'is_running' => str_contains(strtolower($status), 'up'),
                        'is_healthy' => str_contains(strtolower($status), '(healthy)'),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("DockerMonitorService Error: " . $e->getMessage());
        }

        return $containers;
    }

    /**
     * Parse docker stats output into a map.
     */
    private function parseStats($output)
    {
        if (!$output) return [];

        $map = [];
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            $parts = explode('|', $line);
            if (count($parts) < 4) continue;

            $map[$parts[0]] = [
                'cpu' => $parts[1],
                'mem_usage' => $parts[2],
                'mem_perc' => $parts[3],
            ];
        }

        return $map;
    }

    /**
     * Parse status string to a simpler form.
     */
    private function parseStatus($status)
    {
        if (str_contains(strtolower($status), 'up')) {
            return 'online';
        }
        if (str_contains(strtolower($status), 'exited')) {
            return 'offline';
        }
        if (str_contains(strtolower($status), 'restarting')) {
            return 'restarting';
        }
        return 'unknown';
    }

    /**
     * Extrae las últimas N líneas del log de un contenedor específico.
     * Restringe el acceso únicamente a los contenedores del entorno actual.
     *
     * @param string $containerName
     * @param int $lines
     * @return string
     */
    public function getContainerLogs($containerName, $lines = 100)
    {
        try {
            $env = config('app.env') === 'production' ? 'prod' : 'beta';
            $prefix = "dx-";
            $suffix = "-{$env}";

            // Filtro de seguridad: Solo contenedores del stack actual
            if (!str_starts_with($containerName, $prefix) || !str_ends_with($containerName, $suffix)) {
                return "Error: Acceso denegado al contenedor '{$containerName}'. Solo se permiten contenedores del entorno actual.";
            }

            $safeContainer = escapeshellarg($containerName);
            $safeLines = (int) $lines;
            
            // Ejecutar docker logs
            $output = shell_exec("docker logs --tail {$safeLines} {$safeContainer} 2>&1");
            
            return $output ?: "No hay logs disponibles o el contenedor no existe.";
            
        } catch (\Exception $e) {
            Log::error("DockerMonitorService@getContainerLogs Error: " . $e->getMessage());
            return "Error al recuperar logs: " . $e->getMessage();
        }
    }
}
