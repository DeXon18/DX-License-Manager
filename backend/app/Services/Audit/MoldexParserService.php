<?php

namespace App\Services\Audit;

/**
 * MoldexParserService
 * 
 * Parser determinista para archivos .mac de Moldex3D.
 * Extrae metadatos de cabecera y listado de productos desde comentarios.
 */
class MoldexParserService
{
    /**
     * Parsea el contenido completo y devuelve un array estructurado.
     */
    public function parse(string $content): array
    {
        $data = [
            'customer_id'   => null,
            'customer_name' => null,
            'license_mode'  => null,
            'machine_id'    => null,
            'hostname'      => null,
            'expiration'    => null,
            'products'      => []
        ];

        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // 1. Metadatos en comentarios de cabecera
            if (str_starts_with($line, ';')) {
                // Customer ID
                if (preg_match('/Customer ID\s*:\s*([0-9]+)/i', $line, $matches)) {
                    $data['customer_id'] = $matches[1];
                }
                // Customer Name
                elseif (preg_match('/Customer\s*:\s*(.+)/i', $line, $matches)) {
                    $data['customer_name'] = $this->cleanCustomerName($matches[1]);
                }
                // License Mode
                elseif (preg_match('/License Mode\s*:\s*(.+)/i', $line, $matches)) {
                    $data['license_mode'] = trim($matches[1]);
                }
                // Machine ID & Hostname
                elseif (preg_match('/Machine ID\s*:\s*([^\(]+)\(([^\)]+)\)/i', $line, $matches)) {
                    $data['hostname'] = trim($matches[1]);
                    $details = $matches[2];
                    if (str_contains($details, '//')) {
                        $parts = explode('//', $details);
                        $data['machine_id'] = trim(end($parts));
                    } else {
                        $data['machine_id'] = trim($details);
                    }
                }

                // 2. Productos y Expiración (en comentarios de línea de producto)
                // Formato esperado: ;PRODUCTO-MODO-YYYY/MM/DD-CANTIDAD
                if (preg_match('/;(.+)-(?:Floating|Node-Locked).+-(\d{4}\/\d{2}\/\d{2})-(\d+)/i', $line, $matches)) {
                    $productName = trim($matches[1]);
                    $expiry = $matches[2];
                    $quantity = (int)$matches[3];

                    $data['products'][] = [
                        'name' => $productName,
                        'expiration' => $expiry,
                        'quantity' => $quantity
                    ];

                    // Actualizar fecha de expiración global (la más lejana)
                    if (!$data['expiration'] || $expiry > $data['expiration']) {
                        $data['expiration'] = $expiry;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Limpia el nombre del cliente eliminando prefijos de país como [ESP].
     */
    private function cleanCustomerName(string $name): string
    {
        $name = trim($name);
        return preg_replace('/^\[[A-Z]{2,3}\]\s*/i', '', $name);
    }
}
