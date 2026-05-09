<?php

namespace App\Services\Licensing;

use App\Services\Audit\MoldexParserService;
use Illuminate\Support\Str;

class MoldexService
{
    protected $parser;

    public function __construct(MoldexParserService $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Extrae metadatos para nomenclatura y almacenamiento.
     */
    public function extractMetadata(string $content): array
    {
        $data = $this->parser->parse($content);
        
        // Extraer versión (año) desde los productos si es posible, 
        // o usar el año actual como fallback.
        $version = date('Y');
        foreach ($data['products'] as $product) {
            if (preg_match('/-(\d{4})$/', $product['name'], $matches)) {
                $version = $matches[1];
                break;
            }
        }

        return [
            'year'          => $version,
            'customer_id'   => $data['customer_id'] ?? '000000',
            'customer_name' => $data['customer_name'] ?? 'UNKNOWN',
            'client_raw'    => $this->getRawCustomerName($content), // Necesario para mantener el [ESP] en el nombre de archivo
            'mode'          => $this->simplifyMode($data['license_mode']),
            'expiration'    => $data['expiration'] ? str_replace('/', '', $data['expiration']) : date('Ymd'),
        ];
    }

    /**
     * Genera el nombre del archivo según el estándar:
     * AÑO_ID_[PAIS]CLIENTE__TIPO_FECHA.mac
     */
    public function generateFilename(array $metadata): string
    {
        $year   = $metadata['year'];
        $id     = $metadata['customer_id'];
        $client = $metadata['client_raw'];
        $mode   = $metadata['mode'];
        $date   = $metadata['expiration'];

        // Limpiar client de caracteres no válidos para archivos pero mantener [ESP]
        $client = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $client);

        return "{$year}_{$id}_{$client}__{$mode}_{$date}.mac";
    }

    /**
     * Simplifica el modo de licencia para el nombre del archivo.
     */
    private function simplifyMode(?string $mode): string
    {
        if (!$mode) return 'Unknown';
        
        if (stripos($mode, 'Floating') !== false) return 'Floating';
        if (stripos($mode, 'Node-Locked') !== false) return 'NodeLocked';
        
        return Str::studly($mode);
    }

    /**
     * Extrae el nombre del cliente tal cual viene (con prefijo de país) para el nombre del archivo.
     */
    private function getRawCustomerName(string $content): string
    {
        if (preg_match('/Customer\s*:\s*(.+)/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return 'UNKNOWN';
    }
}
