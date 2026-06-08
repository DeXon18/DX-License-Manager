<?php

namespace App\Services\Licensing;

use App\Models\Client;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\Data\ClientNormalizationService;

/**
 * MoldexSyncService
 * 
 * Gestiona la persistencia de los datos parseados de Moldex3D en el Inventario Activo.
 */
class MoldexSyncService
{
    protected ClientNormalizationService $normalizationService;

    public function __construct(ClientNormalizationService $normalizationService)
    {
        $this->normalizationService = $normalizationService;
    }

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
            // 1. Identificar Cliente a través del sistema de normalización inteligente
            $customerName = $parsedData['customer_name'] ?? 'Desconocido';
            if ($customerName === 'Desconocido') {
                $summary['error'] = "No se encontró el nombre del cliente en el archivo.";
                return $summary;
            }

            $normalizationResult = $this->normalizationService->resolve($customerName);
            $client = Client::find($normalizationResult['id']);

            if (!$client) {
                $summary['error'] = "Fallo crítico en la normalización para '{$customerName}'";
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
                    'version'     => $parsedData['version'] ?? null,
                ]
            );

            // 3. Sincronizar Productos
            foreach ($parsedData['products'] as $prodData) {
                $expDate = $this->parseDate($prodData['expiration']);

                LicenseInventoryProduct::updateOrCreate(
                    [
                        'daemon_id'    => $daemon->id,
                        'product_code' => $prodData['code'],
                        'expiration_date' => $expDate,
                    ],
                    [
                        'description'     => $prodData['name'] ?? null,
                        'quantity'        => $prodData['quantity'] ?? 1,
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
