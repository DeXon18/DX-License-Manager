<?php

namespace App\Services\Licensing;

use Illuminate\Support\Str;

class HeedsService
{
    /**
     * Extrae metadatos del contenido de una licencia HEEDS (RCTECH).
     * Prioriza el bloque de cabecera de Siemens.
     */
    /**
     * Extrae metadatos del contenido de una licencia HEEDS (RCTECH).
     * Prioriza el bloque de cabecera de Siemens.
     */
    public function extractMetadata(string $content): array
    {
        $metadata = [
            'sold_to'        => 'UNKNOWN',
            'other_installs' => [],
            'client'         => 'UNKNOWN',
            'version'        => 'V1',
            'hostname'       => 'localhost',
            'hostid'         => 'ANY',
            'expiration'     => 'UNKNOWN',
            'type'           => 'Temporal',
        ];

        // 1. Extraer del bloque de cabecera (Siemens Standard)
        if (preg_match('/Sold-To\/Install:\s*(\d+)/', $content, $m)) {
            $metadata['sold_to'] = trim($m[1]);
        }

        // 2. Otros Sold-To (Unificada)
        if (preg_match('/Other Installs:\s*([^#\n\r]+)/', $content, $matches)) {
            $installs = preg_split('/[,\s]+/', trim($matches[1]), -1, PREG_SPLIT_NO_EMPTY);
            $metadata['other_installs'] = $installs;
        }

        if (preg_match('/Customer Name:\s*([^#\n\r]+)/', $content, $m)) {
            $metadata['client'] = trim($m[1]);
        }

        if (preg_match('/Version:\s*([\d.]+)/', $content, $m)) {
            $metadata['version'] = trim($m[1]);
        }

        // Fallback: Si el nombre del cliente sigue siendo UNKNOWN, buscar en VENDOR_STRING (server_id)
        if ($metadata['client'] === 'UNKNOWN' || $metadata['sold_to'] === 'UNKNOWN') {
            if (preg_match('/VENDOR_STRING="(\d+)\s*-\s*([^"]+)"/', $content, $m)) {
                if ($metadata['sold_to'] === 'UNKNOWN') $metadata['sold_to'] = trim($m[1]);
                if ($metadata['client'] === 'UNKNOWN') $metadata['client'] = trim($m[2]);
            }
        }

        // 3. Extraer Hostname y HostID de la línea SERVER
        if (preg_match('/SERVER\s+(\S+)\s+(\S+)/', $content, $m)) {
            $metadata['hostname'] = $m[1];
            $metadata['hostid']   = $m[2];
            
            if ($metadata['hostname'] === 'YourHostname' && str_contains($metadata['hostid'], 'COMPOSITE=')) {
                $metadata['hostname'] = 'localhost';
            }
            
            // Si tiene MAC/Composite (no es ANY), es Contractual
            if ($metadata['hostid'] !== 'ANY' && !str_contains($metadata['hostid'], 'YourHostname')) {
                $metadata['type'] = 'Contractual';
            }
        }

        // 4. Extraer Fecha de Expiración del primer INCREMENT o FEATURE RCTECH
        if (preg_match('/(?:INCREMENT|FEATURE)\s+\S+\s+RCTECH\s+\S+\s+(\d+-\w+-\d+|permanent)/i', $content, $m)) {
            $metadata['expiration'] = $m[1];
        }

        // 5. Unificada?
        if (!empty($metadata['other_installs'])) {
            $metadata['type'] = 'Unificada';
        }

        // 6. Preparar Sold-To para el nombre del archivo
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

    /**
     * Genera el nombre del archivo según el estándar del portal.
     */
    public function generateFilename(array $metadata): string
    {
        $soldTo   = $metadata['sold_to_filename'] ?? $metadata['sold_to'];
        $client   = Str::upper(str_replace([' ', '.', '-'], '_', trim($metadata['client'])));
        $hostname = Str::upper(str_replace([' ', '.'], '', trim($metadata['hostname'])));
        
        $rawVersion = $metadata['version'];
        $version = $this->normalizeVersion($rawVersion);
        
        $date = $this->formatExpirationDate($metadata['expiration'] ?? null);
        
        if ($metadata['type'] === 'Unificada') {
            return "{$soldTo}_Unificada_{$hostname}_{$client}_HEEDS_V{$version}_Valida_{$date}.lic";
        }

        if ($metadata['type'] === 'Contractual') {
            return "{$soldTo}_{$hostname}_{$client}_HEEDS_V{$version}_Valida_{$date}.lic";
        }

        // Temporal
        return "{$soldTo}_{$client}_HEEDS_V{$version}_TEMP_Valida_{$date}.lic";
    }

    /**
     * Normaliza la versión manteniendo puntos y aplicando YY.MM si es necesario.
     */
    private function normalizeVersion(string $version): string
    {
        $version = ltrim($version, 'vV');
        
        // Si es 2025.10 -> 25.10
        if (preg_match('/^\d{2}(\d{2})\.(\d+)$/', $version, $m)) {
            return $m[1] . '.' . $m[2];
        }
        
        return $version;
    }

    /**
     * Formatea la fecha de expiración al estándar DD-Mmm-YYYY.
     */
    private function formatExpirationDate(?string $date): string
    {
        if (!$date || strtolower($date) === 'permanent' || $date === 'UNKNOWN') {
            return 'Permanent';
        }

        try {
            $d = new \DateTime($date);
            return $d->format('d-M-Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Transforma el contenido migrando rctech a saltd y puerto 29000.
     */
    public function transform(string $content, bool $isTemporal = false): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
        $transformedLines = [];

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // 1. Transformar SERVER (puerto 29000)
            if (str_starts_with($trimmedLine, 'SERVER')) {
                $parts = preg_split('/\s+/', $trimmedLine);
                if (count($parts) >= 3) {
                    $hostname = $parts[1];
                    $hostid   = $parts[2];

                    // Reemplazo YourHostname por localhost SÓLO si tiene COMPOSITE
                    if ($hostname === 'YourHostname' && str_contains($hostid, 'COMPOSITE=')) {
                        $hostname = 'localhost';
                    } elseif ($isTemporal && $hostname === 'ANY') {
                        $hostname = 'localhost';
                    }

                    $line = "SERVER {$hostname} {$hostid} 29000";
                }
            }

            // 2. Transformar VENDOR (rctech -> saltd saltd PORT=29001)
            // Soporta tanto VENDOR RCTECH como VENDOR rctech
            if (preg_match('/^VENDOR\s+RCTECH\b/i', $trimmedLine)) {
                $line = "VENDOR saltd saltd PORT=29001";
            }

            $transformedLines[] = $line;
        }

        return implode("\n", $transformedLines);
    }
}
