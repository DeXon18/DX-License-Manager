<?php

namespace App\Services\AI;

use App\Models\AiAuditResult;
use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InventorySyncService
{
    /**
     * Sincroniza el inventario activo a partir de un resultado de auditoría.
     */
    public function syncFromResult(AiAuditResult $audit)
    {
        $data = $audit->results;
        if (!$data || !$audit->client_id) {
            Log::info("InventorySync: Saltando sincronización, falta data o client_id.");
            return;
        }

        $soldTo = $data['sold_to'] ?? $audit->sold_to;
        $daemonName = $data['vendor_daemon'] ?? 'unknown';
        
        if (!$soldTo || $daemonName === 'unknown') {
            Log::warning("InventorySync: Missing sold_to or daemon for audit {$audit->uuid}");
            return;
        }

        // 1. Sincronizar el Daemon (Contenedor)
        $daemon = LicenseInventoryDaemon::updateOrCreate(
            [
                'client_id' => $audit->client_id,
                'sold_to' => $soldTo,
                'daemon' => $daemonName,
            ],
            [
                'hostname' => $data['server_hostname'] ?? null,
                'composite' => $data['server_host_id'] ?? null,
                'hardware_id' => $this->extractHardwareId($data),
                'additional_sold_tos' => $data['additional_sold_tos'] ?? [],
                'version' => $data['version'] ?? null,
                'type' => $this->determineType($data),
            ]
        );

        // 2. Sincronizar Productos
        $products = $data['products'] ?? [];
        foreach ($products as $prodData) {
            if (empty($prodData['product_code'])) continue;

            $productCode = $prodData['product_code'];
            $hostId = $prodData['node_locked_host_id'] ?? null;

            $expDate = $this->parseDate($prodData['expiration_date'] ?? null);

            LicenseInventoryProduct::updateOrCreate(
                [
                    'daemon_id' => $daemon->id,
                    'product_code' => $productCode,
                    'node_locked_host_id' => $hostId,
                    'expiration_date' => $expDate,
                ],
                [
                    'description' => $prodData['description'] ?? null,
                    'quantity' => $prodData['quantity'] ?? 1,
                    'status' => 'active', // Lo marcamos activo inicialmente
                ]
            );
        }

        // 3. Resolver duplicados y estados (superseded)
        $this->resolveSupersededProducts($daemon->id);

        Log::info("InventorySync: Sincronización completada para Sold-To {$soldTo} / {$daemonName}");
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
            if ($group->count() > 1) {
                $sorted = $group->sortByDesc(function ($item) {
                    return $item->expiration_date ? $item->expiration_date->timestamp : PHP_INT_MAX;
                })->values();

                $newest = $sorted->first();
                if ($newest->status !== 'active') {
                    $newest->update(['status' => 'active']);
                }

                for ($i = 1; $i < $sorted->count(); $i++) {
                    // Solo marcar como superseded si la licencia ya ha caducado (al día siguiente)
                    if ($sorted[$i]->expiration_date && $sorted[$i]->expiration_date->isPast()) {
                        if ($sorted[$i]->status !== 'superseded') {
                            $sorted[$i]->update(['status' => 'superseded']);
                        }
                    }
                }
            }
        }

        // 2. Procesar Flotantes / Sin Host ID (Pendientes de MAC / Paquetes)
        $floatingProducts = $allProducts->filter(fn($p) => empty($p->node_locked_host_id))
            ->groupBy('product_code');

        foreach ($floatingProducts as $productCode => $group) {
            // Agrupar por paquete (misma cantidad + mismo mes y día de expiración)
            $subGroups = $group->groupBy(function ($item) {
                if (!$item->expiration_date) {
                    return $item->quantity . '|PERMANENT';
                }
                return $item->quantity . '|' . $item->expiration_date->format('m-d');
            });

            foreach ($subGroups as $subGroup) {
                if ($subGroup->count() > 1) {
                    // Ordenar por año/timestamp descendente (la versión de 2027 reemplaza a la de 2026)
                    $sorted = $subGroup->sortByDesc(function ($item) {
                        return $item->expiration_date ? $item->expiration_date->timestamp : PHP_INT_MAX;
                    })->values();

                    $newest = $sorted->first();
                    if ($newest->status !== 'active') {
                        $newest->update(['status' => 'active']);
                    }

                    for ($i = 1; $i < $sorted->count(); $i++) {
                        // Solo marcar como superseded si la licencia ya ha caducado (al día siguiente)
                        if ($sorted[$i]->expiration_date && $sorted[$i]->expiration_date->isPast()) {
                            if ($sorted[$i]->status !== 'superseded') {
                                $sorted[$i]->update(['status' => 'superseded']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Extrae el ID de hardware si está presente en el host_id del servidor.
     */
    private function extractHardwareId(array $data)
    {
        $hostId = $data['server_host_id'] ?? '';
        if (str_contains($hostId, 'UG_HWKEY_ID=')) {
            return str_replace('UG_HWKEY_ID=', '', $hostId);
        }
        
        // Si el hostId es un número corto (<= 10 dígitos), probablemente sea un Dongle ID
        if (is_numeric($hostId) && strlen($hostId) <= 10) {
            return $hostId;
        }

        return null;
    }

    /**
     * Determina el tipo de licencia (floating, node-locked, dongle).
     */
    private function determineType(array $data)
    {
        $hostId = $data['server_host_id'] ?? '';
        if (str_contains($hostId, 'UG_HWKEY_ID=') || (is_numeric($hostId) && strlen($hostId) <= 10)) {
            return 'dongle';
        }
        
        $products = $data['products'] ?? [];
        foreach ($products as $p) {
            $prodHostId = $p['node_locked_host_id'] ?? '';
            if (!empty($prodHostId)) {
                // Si el hostID del producto es un Dongle ID
                if (str_contains($prodHostId, 'HWKEY') || (is_numeric($prodHostId) && strlen($prodHostId) <= 10)) {
                    return 'dongle';
                }
                return 'node-locked';
            }
        }

        return 'floating';
    }

    /**
     * Parsea la fecha de expiración manejando casos especiales.
     */
    private function parseDate($dateStr)
    {
        if (!$dateStr) return null;
        
        $specialCases = ['permanent', '9999-12-31', 'any', 'uncounted'];
        if (in_array(strtolower($dateStr), $specialCases)) {
            return null;
        }

        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("InventorySync: Error parseando fecha '{$dateStr}': " . $e->getMessage());
            return null;
        }
    }
}
