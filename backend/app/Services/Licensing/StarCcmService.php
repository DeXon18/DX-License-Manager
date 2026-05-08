<?php

namespace App\Services\Licensing;

use Illuminate\Support\Str;

class StarCcmService
{
    /**
     * Extrae metadatos del contenido de una licencia STAR-CCM+.
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

        // 1. Extraer Sold-To/Install
        if (preg_match('/Sold-To\/Install:\s*(\d+)/', $content, $m)) {
            $metadata['sold_to'] = trim($m[1]);
        }

        // 2. Extraer Customer Name
        if (preg_match('/Customer Name:\s*([^#\n\r]+)/', $content, $m)) {
            $metadata['client'] = trim($m[1]);
        }

        // 3. Extraer Version
        if (preg_match('/Version:\s*([\d.]+)/', $content, $m)) {
            $metadata['version'] = trim($m[1]);
        }

        // 4. Extraer Hostname y HostID de la línea SERVER
        if (preg_match('/SERVER\s+(\S+)\s+(\S+)/', $content, $m)) {
            $metadata['hostname'] = $m[1];
            $metadata['hostid']   = $m[2];
            
            // Si tiene MAC/Composite (no es ANY), es Contractual
            if ($metadata['hostid'] !== 'ANY' && !str_contains($metadata['hostid'], 'YourHostname')) {
                $metadata['type'] = 'Contractual';
            }
        }

        // 5. Extraer Fecha de Expiración del primer INCREMENT
        if (preg_match('/INCREMENT\s+\S+\s+cdlmd\s+\S+\s+(\d+-\w+-\d+)/', $content, $m)) {
            $metadata['expiration'] = $m[1];
        }

        return $metadata;
    }

    /**
     * Genera el nombre del archivo según el estándar del portal.
     */
    public function generateFilename(array $metadata): string
    {
        $soldTo   = $metadata['sold_to'];
        $client   = Str::upper(str_replace([' ', '.'], '-', $metadata['client']));
        $hostname = Str::upper(str_replace([' ', '.'], '', $metadata['hostname']));
        $version  = $metadata['version'];
        $date     = date('dmY');
        
        // SOLDTO_HOSTNAME_CLIENTE_STARCCM_V{VERSION}_Valida_DDMMYYYY.lic
        if ($metadata['type'] === 'Contractual') {
            return "{$soldTo}_{$hostname}_{$client}_STARCCM_V{$version}_Valida_{$date}.lic";
        }

        // Temporal: SOLDTO_CLIENTE_STARCCM_V{VERSION}_TEMP_Valida_DDMMYYYY.lic
        return "{$soldTo}_{$client}_STARCCM_V{$version}_TEMP_Valida_{$date}.lic";
    }

    /**
     * Transforma el contenido migrando cdlmd a saltd y puerto 29000.
     */
    public function transform(string $content): string
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
                    $line = "SERVER {$hostname} {$hostid} 29000";
                }
            }

            // 2. Transformar VENDOR (cdlmd -> saltd saltd PORT=29001)
            if (str_starts_with($trimmedLine, 'VENDOR')) {
                $line = "VENDOR saltd saltd PORT=29001";
            }

            $transformedLines[] = $line;
        }

        return implode("\n", $transformedLines);
    }
}
