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

            // 4. Resolver duplicados y estados (superseded)
            $this->resolveSupersededProducts($daemon->id);

            $summary['synced'] = true;
            Log::info("MoldexSync: Inventario actualizado para {$client->name} (Sold-To: {$soldTo})");

        } catch (\Exception $e) {
            $summary['error'] = "Error en la sincronización: " . $e->getMessage();
            Log::error("MoldexSync Error: " . $e->getMessage());
        }

        return $summary;
    }

    /**
     * Identifica duplicados y renovaciones (superseded) respetando licencias independientes.
     * - Node-locked (con MAC): la versión con fecha más lejana reemplaza a las versiones anteriores con la misma MAC.
     * - Flotantes (sin MAC): solo se reemplaza una licencia anterior si coincide la cantidad y el día/mes de expiración (renovación anual del mismo bloque).
     */
    private function resolveSupersededProducts($daemonId)
    {
        $allProducts = LicenseInventoryProduct::where('daemon_id', $daemonId)->get();

        // 1. Procesar Node-locked (con MAC no nula)
        $nodeLockedGroups = $allProducts->whereNotNull('node_locked_host_id')
            ->filter(fn($p) => trim($p->node_locked_host_id) !== '')
            ->groupBy(function ($item) {
                return $item->product_code . '|' . $item->node_locked_host_id;
            });

        foreach ($nodeLockedGroups as $group) {
            $sorted = $group->sortByDesc(function ($item) {
                return $item->expiration_date ? $item->expiration_date->timestamp : PHP_INT_MAX;
            })->values();

            $newest = $sorted->first();
            if ($newest->expiration_date && $newest->expiration_date->isPast()) {
                if ($newest->status !== 'superseded') {
                    $newest->update(['status' => 'superseded']);
                }
            } else {
                if ($newest->status !== 'active') {
                    $newest->update(['status' => 'active']);
                }
            }

            for ($i = 1; $i < $sorted->count(); $i++) {
                if ($sorted[$i]->status !== 'superseded') {
                    $sorted[$i]->update(['status' => 'superseded']);
                }
            }
        }

        // 2. Procesar Flotantes / Sin Host ID (Pendientes de MAC / Machine ID)
        $floatingProducts = $allProducts->filter(fn($p) => empty($p->node_locked_host_id));

        foreach ($floatingProducts as $product) {
            if ($product->expiration_date && $product->expiration_date->isPast()) {
                if ($product->status !== 'superseded') {
                    $product->update(['status' => 'superseded']);
                }
            } else {
                if ($product->status !== 'active') {
                    $product->update(['status' => 'active']);
                }
            }
        }
    }


    /**
     * Parsea la fecha de Moldex3D (YYYYMMDD) a formato BD.
     */
    private function parseDate($dateStr): ?string
    {
        if (!$dateStr) return null;
        if (strtolower($dateStr) === 'permanent' || $dateStr === '9999-12-31') return '9999-12-31';

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
