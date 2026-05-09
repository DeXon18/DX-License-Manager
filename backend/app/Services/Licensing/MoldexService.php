<?php

namespace App\Services\Licensing;

use App\Services\Audit\MoldexParserService;
use Illuminate\Support\Str;

/**
 * MoldexService
 * 
 * Gestiona la lógica de negocio para licencias Moldex3D:
 * Nomenclatura, rutas de almacenamiento y organización de metadatos.
 */
class MoldexService
{
    protected $parser;

    public function __construct(MoldexParserService $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Extrae metadatos para la nomenclatura del archivo.
     * Formato: AÑO_ID_[PAIS]CLIENTE__TIPO_FECHA
     */
    public function extractMetadata(string $content): array
    {
        $parsed = $this->parser->parse($content);
        
        // Extraer país del nombre crudo si existe (ej: [ESP]Nombre)
        $country = 'INT';
        if (preg_match('/^\[([A-Z]{2,3})\]/i', $content, $matches)) {
            $country = strtoupper($matches[1]);
        }

        // Determinar año (usualmente el actual o el de inicio de contrato)
        $year = date('Y');
        if (preg_match('/Period\s*:\s*(\d{4})/i', $content, $matches)) {
            $year = $matches[1];
        }

        return [
            'year'          => $year,
            'customer_id'   => $parsed['customer_id'] ?? '000000',
            'customer_name' => $parsed['customer_name'] ?? 'UNKNOWN',
            'client_raw'    => $this->getRawClientName($content),
            'mode'          => $parsed['license_mode'] ?? 'Unknown',
            'expiration'    => $parsed['expiration'] ?? date('Ymd'),
            'country'       => $country
        ];
    }

    /**
     * Genera el nombre de archivo estandarizado.
     */
    public function generateFilename(array $metadata): string
    {
        $client = $metadata['client_raw'] ?? $metadata['customer_name'];
        $mode   = $metadata['mode'];
        $date   = $metadata['expiration'];
        $id     = $metadata['customer_id'];
        $year   = $metadata['year'];

        // AÑO_ID_CLIENTE__TIPO_FECHA.mac
        return "{$year}_{$id}_{$client}__{$mode}_{$date}.mac";
    }

    /**
     * Obtiene el nombre del cliente con el prefijo de país si está presente.
     */
    private function getRawClientName(string $content): string
    {
        if (preg_match('/Customer\s*:\s*(.+)/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return 'UNKNOWN';
    }

    /**
     * Obtiene la ruta de almacenamiento privada.
     */
    public function getStoragePath(string $clientName, string $year): string
    {
        $slug = Str::slug($clientName);
        return "licenses/moldex3d/{$slug}/{$year}";
    }
}
