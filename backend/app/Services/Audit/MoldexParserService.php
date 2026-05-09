<?php

namespace App\Services\Audit;

/**
 * MoldexParserService
 * 
 * Parser determinista para archivos .mac de Moldex3D.
 * Extrae metadatos de cabecera y listado de productos.
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

            // 1. Metadatos en comentarios (pueden empezar por ; o #)
            if (preg_match('/^[;#]/', $line)) {
                // Customer ID / Project ID
                if (preg_match('/(?:Customer|Project) ID\s*:\s*([0-9]+)/i', $line, $matches)) {
                    $data['customer_id'] = $matches[1];
                }
                // Customer Name
                elseif (preg_match('/Customer\s*:\s*(.+)/i', $line, $matches)) {
                    $data['customer_name'] = $this->cleanCustomerName($matches[1]);
                }
                // License Mode / Type
                elseif (preg_match('/License (?:Mode|Type)\s*:\s*(.+)/i', $line, $matches)) {
                    $mode = trim($matches[1]);
                    if (stripos($mode, 'Floating') !== false) $mode = 'Floating';
                    elseif (stripos($mode, 'Node-Locked') !== false || stripos($mode, 'Node Locked') !== false) $mode = 'Node-Locked';
                    $data['license_mode'] = $mode;
                }
                // Machine ID / MAC / Hostname
                elseif (preg_match('/(?:Machine ID|MAC)\s*:\s*(.+)/i', $line, $matches)) {
                    $rawMachine = trim($matches[1]);
                    // Formato hostname(id) o solo id
                    if (preg_match('/^([^\s(]+)\s*\((.+)\)$/', $rawMachine, $subMatches)) {
                        $data['hostname'] = trim($subMatches[1]);
                        $details = $subMatches[2];
                        if (str_contains($details, '//')) {
                            $parts = explode('//', $details);
                            $data['machine_id'] = trim(end($parts));
                        } else {
                            $data['machine_id'] = trim($details);
                        }
                    } else {
                        $data['machine_id'] = $rawMachine;
                    }
                }
                // Fecha de expiración (generalmente en comentarios de producto o periodo)
                if (preg_match('/(\d{4}\/\d{2}\/\d{2})/', $line, $matches)) {
                    $date = str_replace('/', '', $matches[1]);
                    if (!$data['expiration'] || $date > $data['expiration']) {
                        $data['expiration'] = $date;
                    }
                }
            }

            // 2. Líneas INCREMENT (Formato estándar FlexLM usado por Moldex)
            // INCREMENT M3D_ADV moldex3d 2027.0 20270114 1 ...
            if (str_starts_with($line, 'INCREMENT')) {
                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 6) {
                    $code = $parts[1];
                    $expiry = $parts[4];
                    $qty = (int)$parts[5];

                    $data['products'][] = [
                        'code' => $code,
                        'name' => $this->getFriendlyName($code),
                        'expiration' => $expiry,
                        'quantity' => $qty
                    ];

                    if (!$data['expiration'] || ($expiry !== 'permanent' && $expiry > $data['expiration'])) {
                        if ($expiry !== 'permanent') {
                            $data['expiration'] = $expiry;
                        }
                    }
                }
            }

            // 3. Fallback: Comentarios de producto antiguos (;PRODUCTO-MODO-FECHA-CANTIDAD)
            if (preg_match('/^[;#](.+)-(?:Floating|Node-Locked).+-(\d{4}\/\d{2}\/\d{2})-(\d+)/i', $line, $matches)) {
                $code = trim($matches[1]);
                $expiry = str_replace('/', '', $matches[2]);
                $qty = (int)$matches[3];

                // Evitar duplicados si ya se capturó por INCREMENT
                $exists = false;
                foreach ($data['products'] as $p) {
                    if ($p['code'] === $code) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $data['products'][] = [
                        'code' => $code,
                        'name' => $this->getFriendlyName($code),
                        'expiration' => $expiry,
                        'quantity' => $qty
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Devuelve el nombre amigable del módulo.
     */
    public function getFriendlyName(string $code): string
    {
        $mapping = [
            'M3D_ADV'           => 'Moldex3D Advanced',
            'M3D_FEA'           => 'FEA Interface',
            'STUDIO'            => 'Moldex3D Studio',
            'FLOW'              => 'Flow Analysis',
            'PACK'              => 'Packing Analysis',
            'COOL'              => 'Cooling Analysis',
            'WARP'              => 'Warpage Analysis',
            'FIBER'             => 'Fiber Orientation',
            'REACTIVE'          => 'Reactive Injection',
            'ENCAP'             => 'IC Packaging',
            'MUCELL'            => 'MuCell Injection',
            'OPT'               => 'Expert/Optimization',
            '3DFE'              => '3D Solid Mesh',
            'MDE_SOLVER'        => 'Solver Engine',
        ];

        $cleanCode = strtoupper(trim($code));
        return $mapping[$cleanCode] ?? $code;
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
