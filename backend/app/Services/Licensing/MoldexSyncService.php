<?php

namespace App\Services\Licensing;

use App\Models\Client;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * MoldexSyncService
 * 
 * Gestiona la persistencia de los datos parseados de Moldex3D en el Inventario Activo.
 */
class MoldexSyncService
{
    /**
     * Sincroniza los datos de Moldex3D con el Inventario Activo.
     * 
     * @param array $parsedData Datos devueltos por MoldexParserService
     * @return array Resumen de la sincronización
     */
    public function sync(array $parsedData): array
    {
        $summary = [
            'synced' => false,
            'client_name' => null,
            'error' => null
        ];

        try {
            // 1. Identificar Cliente
            $client = $this->findClient($parsedData);
            if (!$client) {
                $summary['error'] = "No se pudo identificar un cliente para '" . ($parsedData['customer_name'] ?? 'Desconocido') . "'";
                return $summary;
            }

            $summary['client_name'] = $client->name;

            // 2. Sincronizar Daemon
            // El Sold-To en Moldex es el Customer ID. Si no existe, generamos uno ficticio.
            $soldTo = $parsedData['customer_id'] ?? 'M3D-' . $client->id;

            $daemon = LicenseInventoryDaemon::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'sold_to'   => $soldTo,
                    'daemon'    => 'moldex3d',
                ],
                [
                    'hostname'    => $parsedData['hostname'] ?? null,
                    'hardware_id' => $parsedData['machine_id'] ?? null,
                    'type'        => strtolower($parsedData['license_mode'] ?? 'floating'),
                ]
            );

            // 3. Sincronizar Productos
            foreach ($parsedData['products'] as $prodData) {
                LicenseInventoryProduct::updateOrCreate(
                    [
                        'daemon_id'    => $daemon->id,
                        'product_code' => $prodData['code'],
                    ],
                    [
                        'description'     => $prodData['name'] ?? null,
                        'quantity'        => $prodData['quantity'] ?? 1,
                        'expiration_date' => $this->parseDate($prodData['expiration']),
                        'status'          => 'active'
                    ]
                );
            }

            $summary['synced'] = true;
            Log::info("MoldexSync: Inventario actualizado para {$client->name} (Sold-To: {$soldTo})");

        } catch (\Exception $e) {
            $summary['error'] = "Error en la sincronización: " . $e->getMessage();
            Log::error("MoldexSync Error: " . $e->getMessage());
        }

        return $summary;
    }

    /**
     * Busca un cliente en la base de datos basándose en el nombre parseado.
     */
    private function findClient(array $data): ?Client
    {
        $name = $data['customer_name'];
        if (!$name) return null;

        // Búsqueda exacta
        $client = Client::where('name', $name)->first();
        if ($client) return $client;

        // Búsqueda parcial (fuzzy)
        $client = Client::where('name', 'LIKE', "%{$name}%")->first();
        if ($client) return $client;

        // Intentar limpiar el nombre un poco más (remover SL, SA, etc)
        $cleanName = preg_replace('/\b(S\.?L\.?|S\.?A\.?|I\+D\+I)\b/i', '', $name);
        $cleanName = trim($cleanName);
        
        if ($cleanName !== $name && !empty($cleanName)) {
            return Client::where('name', 'LIKE', "%{$cleanName}%")->first();
        }

        return null;
    }

    /**
     * Parsea la fecha de Moldex3D (YYYYMMDD) a formato BD.
     */
    private function parseDate($dateStr): ?string
    {
        if (!$dateStr || strtolower($dateStr) === 'permanent') return null;

        try {
            // Moldex suele usar YYYYMMDD o YYYY/MM/DD
            $cleanDate = str_replace(['/', '-'], '', $dateStr);
            if (strlen($cleanDate) === 8) {
                return Carbon::createFromFormat('Ymd', $cleanDate)->format('Y-m-d');
            }
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("MoldexSync: No se pudo parsear fecha '{$dateStr}'");
            return null;
        }
    }
}
