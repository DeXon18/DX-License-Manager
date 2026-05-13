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
        $soldTo   = $metadata['sold_to_filename'] ?? $metadata['sold_to'] ?? 'UNKNOWN';
        $hostname = Str::upper(str_replace([' ', '.'], '', $metadata['hostname'] ?? 'NOHOST'));
        
        // Cliente con guion bajo y en mayúsculas
        $client   = Str::upper(str_replace([' ', '.', '-'], '_', $metadata['client'] ?? 'CLIENT'));
        
        // Versión con prefijo V
        $rawVersion = $metadata['version'] ?? 'V1';
        $version = str_starts_with(strtoupper($rawVersion), 'V') ? strtoupper($rawVersion) : "V{$rawVersion}";
        
        // Fecha de caducidad formateada (DD-Mmm-YYYY)
        $date = $this->formatExpirationDate($metadata['expiration'] ?? null);
        
        $type = $metadata['type'] ?? 'Standard';

        switch ($type) {
            case 'Temporal':
                return "{$soldTo}_{$client}_{$version}_TEMP_Valida_{$date}.lic";
            
            case 'Dongle':
                return "{$soldTo}_{$client}_{$version}_DongleUSB_Valida_{$date}.lic";
            
            case 'Unificada':
                return "{$soldTo}_Unificada_{$hostname}_{$client}_{$version}_Valida_{$date}.lic";
            
            case 'Standard':
            default:
                return "{$soldTo}_{$hostname}_{$client}_{$version}_Valida_{$date}.lic";
        }
    }

    /**
     * Formatea la fecha de expiración al estándar DD-Mmm-YYYY.
     */
    private function formatExpirationDate(?string $date): string
    {
        if (!$date || strtolower($date) === 'permanent') {
            return 'Permanent';
        }

        try {
            $d = new \DateTime($date);
            // Formato: 14-Mar-2026 (M corta con primera mayúscula)
            return $d->format('d-M-Y');
        } catch (\Exception $e) {
            return $date; // Fallback al original si falla el parseo
        }
    }

    /**
     * Determina el tipo de licencia basándose en el contenido y metadatos detectados.
     */
    public function detectType(string $content, array $metadata = []): string
    {
        if (!str_contains($content, 'SERVER') && str_contains($content, 'UG_HWKEY_ID')) {
            return 'Dongle';
        }

        // Si tiene múltiples Sold-To detectados en el header, es unificada
        if (!empty($metadata['other_installs'])) {
            return 'Unificada';
        }

        if (str_contains($content, 'YourHostname') || str_contains($content, 'ANY')) {
            return 'Temporal';
        }

        return 'Standard';
    }

    /**
     * Extrae metadatos básicos del contenido.
     */
    public function extractMetadata(string $content): array
    {
        $metadata = [
            'sold_to'        => '10300000',
            'other_installs' => [],
            'hostname'       => 'localhost',
            'client'         => 'Default',
            'version'        => '2512',
            'expiration'     => null,
            'type'           => 'Standard',
        ];

        // 1. Extraer Sold-To Principal
        if (preg_match('/Sold-To\/Install:\s*(\d+)/', $content, $matches)) {
            $metadata['sold_to'] = $matches[1];
        }

        // 2. Extraer Otros Sold-To (Unificada)
        if (preg_match('/Other Installs:\s*([^#\n\r]+)/', $content, $matches)) {
            $installs = preg_split('/[,\s]+/', trim($matches[1]), -1, PREG_SPLIT_NO_EMPTY);
            $metadata['other_installs'] = $installs;
        }

        // 3. Extraer Customer Name
        if (preg_match('/Customer Name:\s*([^\r\n#]+)/', $content, $matches)) {
            $metadata['client'] = trim($matches[1]);
        }

        // 4. Extraer Hostname de la línea SERVER
        if (preg_match('/SERVER\s+([^\s]+)/', $content, $matches)) {
            $metadata['hostname'] = $matches[1];
        }

        // 5. Extraer Versión del primer INCREMENT
        if (preg_match('/INCREMENT\s+\S+\s+ugslmd\s+([\d.]+)/', $content, $matches)) {
            $version = $matches[1];
            // Si es formato 2025.12 -> 25.12
            if (preg_match('/^\d{2}(\d{2})\.(\d+)$/', $version, $vMatches)) {
                $metadata['version'] = $vMatches[1] . '.' . $vMatches[2];
            } else {
                $metadata['version'] = $version;
            }
        }

        // 6. Extraer Fecha de Caducidad del primer INCREMENT
        if (preg_match('/INCREMENT\s+\S+\s+ugslmd\s+[\d.]+\s+(\d+-\w+-\d+|permanent)/i', $content, $matches)) {
            $metadata['expiration'] = $matches[1];
        }

        // 7. Determinar Tipo
        $metadata['type'] = $this->detectType($content, $metadata);

        // 8. Preparar Sold-To para el nombre del archivo (Lógica S1-S2 o S1_Multi)
        $allIds = array_merge([$metadata['sold_to']], $metadata['other_installs']);
        if (count($allIds) > 1) {
            if (count($allIds) <= 3) {
                $metadata['sold_to_filename'] = implode('-', $allIds);
            } else {
                $metadata['sold_to_filename'] = $allIds[0] . '_Multi';
            }
        }

        return $metadata;
    }
}
