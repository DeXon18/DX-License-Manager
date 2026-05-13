<?php

namespace App\Services\Licensing;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NXSuiteService
{
    /**
     * Motores disponibles.
     */
    const MOTOR_LEGACY = 'legacy'; // 28000 + ugslmd
    const MOTOR_SALT   = 'salt';   // 29000 + saltd

    /**
     * Transforma el contenido de una licencia según el motor seleccionado.
     */
    public function transform(string $content, string $motor, bool $isTemporal7Days = false): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
        $transformedLines = [];

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // 1. Transformación de línea SERVER
            if (str_starts_with($trimmedLine, 'SERVER')) {
                $line = $this->transformServerLine($line, $motor, $isTemporal7Days);
            }

            // 2. Transformación de línea VENDOR
            if (str_starts_with($trimmedLine, 'VENDOR')) {
                $line = $this->transformVendorLine($line, $motor);
            }

            $transformedLines[] = $line;
        }

        return implode("\n", $transformedLines);
    }

    /**
     * Lógica de transformación para la línea SERVER.
     */
    private function transformServerLine(string $line, string $motor, bool $isTemporal7Days): string
    {
        $parts = preg_split('/\s+/', trim($line));
        
        // SERVER [hostname] [hostid] [port]
        if (count($parts) < 4) return $line;

        $hostname = $parts[1];
        $hostid   = $parts[2];
        $port     = $parts[3];

        // En licencias temporales, siempre usar localhost para evitar fallos de resolución
        if ($isTemporal7Days && ($hostname === 'YourHostname' || $hostname === 'ANY')) {
            $hostname = 'localhost';
        }

        if ($motor === self::MOTOR_LEGACY) {
            $port = '28000';
        } else {
            $port = '29000';
        }

        return "SERVER {$hostname} {$hostid} {$port}";
    }

    /**
     * Lógica de transformación para la línea VENDOR.
     */
    private function transformVendorLine(string $line, string $motor): string
    {
        if ($motor === self::MOTOR_SALT && preg_match('/^VENDOR\s+ugslmd\b/i', trim($line))) {
            // VENDOR ugslmd -> VENDOR saltd saltd PORT=29001
            return "VENDOR saltd saltd PORT=29001";
        }

        // Si no es la línea de definición del vendor o es legacy, mantener original
        return $line;
    }

    /**
     * Genera el nombre del archivo según el tipo de licencia y metadatos.
     */
    public function generateFilename(array $metadata): string
    {
        $soldTo   = $metadata['sold_to'] ?? 'UNKNOWN';
        $hostname = Str::upper(str_replace([' ', '.'], '', $metadata['hostname'] ?? 'NOHOST'));
        $client   = Str::upper(str_replace([' ', '.'], '-', $metadata['client'] ?? 'CLIENT'));
        $version  = $metadata['version'] ?? 'V1';
        $date     = date('dmY'); // Formato DDMMYYYY (07052026)
        $type     = $metadata['type'] ?? 'Standard';

        // SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_FECHA.lic
        switch ($type) {
            case 'Temporal':
                // Sin hostname para temporales: SOLDTO_CLIENTE_VERSION_TEMP_Valida_FECHA.lic
                return "{$soldTo}_{$client}_{$version}_TEMP_Valida_{$date}.lic";
            
            case 'Dongle':
                // Sin Hostname
                return "{$soldTo}_{$client}_{$version}_DongleUSB_Valida_{$date}.lic";
            
            case 'Unificada':
                return "{$soldTo}_Unificada_{$hostname}_{$client}_{$version}_Valida_{$date}.lic";
            
            case 'Standard':
            default:
                return "{$soldTo}_{$hostname}_{$client}_{$version}_Valida_{$date}.lic";
        }
    }

    /**
     * Determina si la licencia es tipo Dongle basándose en el contenido.
     */
    public function detectType(string $content): string
    {
        if (!str_contains($content, 'SERVER') && str_contains($content, 'UG_HWKEY_ID')) {
            return 'Dongle';
        }

        if (str_contains($content, 'YourHostname') || str_contains($content, 'ANY')) {
            return 'Temporal';
        }

        return 'Standard';
    }

    /**
     * Extrae metadatos básicos del contenido (Simulando lo que haría el parser en Parte 2, 
     * pero necesario para nombrar el archivo en Parte 1).
     */
    public function extractMetadata(string $content): array
    {
        $metadata = [
            'sold_to'  => '10300000',
            'hostname' => 'localhost',
            'client'   => 'Default',
            'version'  => '2512',
            'date'     => date('Ymd'),
            'type'     => $this->detectType($content),
        ];

        // Intento de extraer Sold-To de los comentarios
        if (preg_match('/Sold-To\/Install:\s*(\d+)/', $content, $matches)) {
            $metadata['sold_to'] = $matches[1];
        }

        // Intento de extraer Customer Name
        if (preg_match('/Customer Name:\s*([^\r\n#]+)/', $content, $matches)) {
            $metadata['client'] = trim($matches[1]);
        }

        // Intento de extraer Hostname de la línea SERVER
        if (preg_match('/SERVER\s+([^\s]+)/', $content, $matches)) {
            $metadata['hostname'] = $matches[1];
        }

        return $metadata;
    }
}
