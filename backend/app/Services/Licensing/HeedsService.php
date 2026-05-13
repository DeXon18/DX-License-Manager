<?php

namespace App\Services\Licensing;

use Illuminate\Support\Str;

class HeedsService
{
    /**
     * Extrae metadatos del contenido de una licencia HEEDS (RCTECH).
     * Prioriza el bloque de cabecera de Siemens.
     */
    public function extractMetadata(string $content): array
    {
        $metadata = [
            'sold_to'    => 'UNKNOWN',
            'client'     => 'UNKNOWN',
            'version'    => 'V1',
            'hostname'   => 'localhost',
            'hostid'     => 'ANY',
            'expiration' => 'UNKNOWN',
            'type'       => 'Temporal',
        ];

        // 1. Extraer del bloque de cabecera (Siemens Standard)
        if (preg_match('/Sold-To\/Install:\s*(\d+)/', $content, $m)) {
            $metadata['sold_to'] = trim($m[1]);
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

        // 2. Extraer Hostname y HostID de la línea SERVER
        if (preg_match('/SERVER\s+(\S+)\s+(\S+)/', $content, $m)) {
            $metadata['hostname'] = $m[1];
            $metadata['hostid']   = $m[2];
            
            // Si tiene MAC/Composite (no es ANY), es Contractual
            if ($metadata['hostid'] !== 'ANY' && !str_contains($metadata['hostid'], 'YourHostname')) {
                $metadata['type'] = 'Contractual';
            }
        }

        // 3. Extraer Fecha de Expiración del primer INCREMENT RCTECH
        if (preg_match('/INCREMENT\s+\S+\s+RCTECH\s+\S+\s+(\d+-\w+-\d+)/', $content, $m)) {
            $metadata['expiration'] = $m[1];
        }

        return $metadata;
    }

    /**
     * Genera el nombre del archivo según el estándar del portal.
     * Estándar: SOLDTO_HOSTNAME_CLIENTE_HEEDS_V{VERSION}_Valida_DDMMYYYY.lic
     */
    public function generateFilename(array $metadata): string
    {
        $soldTo   = $metadata['sold_to'];
        $client   = Str::upper(str_replace([' ', '.'], '-', trim($metadata['client'])));
        $hostname = Str::upper(str_replace([' ', '.'], '', trim($metadata['hostname'])));
        $version  = $metadata['version'];
        $date     = date('dmY');
        
        // Limpiar posibles caracteres extraños en el cliente (provenientes del block comment)
        $client = preg_replace('/[^A-Z0-9\-]/', '', $client);

        if ($metadata['type'] === 'Contractual') {
            return "{$soldTo}_{$hostname}_{$client}_HEEDS_V{$version}_Valida_{$date}.lic";
        }

        // Temporal: SOLDTO_CLIENTE_HEEDS_V{$version}_TEMP_Valida_{$date}.lic
        return "{$soldTo}_{$client}_HEEDS_V{$version}_TEMP_Valida_{$date}.lic";
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

                    if ($isTemporal && ($hostname === 'YourHostname' || $hostname === 'ANY')) {
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
