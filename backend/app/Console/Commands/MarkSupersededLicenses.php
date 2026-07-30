<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LicenseInventoryDaemon;
use Illuminate\Support\Facades\Log;

class MarkSupersededLicenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dx:mark-superseded {--reset : Restaura primero todos los productos a active antes de reevaluar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retroactively marks old duplicate products as superseded based on product_code and node_locked_host_id.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando la revisión retroactiva de licencias...");
        
        if ($this->option('reset')) {
            $resetCount = \App\Models\LicenseInventoryProduct::where('status', 'superseded')->update(['status' => 'active']);
            $this->info("Restauradas {$resetCount} licencias de superseded a active.");
        }

        $daemons = LicenseInventoryDaemon::with('products')->get();
        $totalSuperseded = 0;

        foreach ($daemons as $daemon) {
            $allProducts = $daemon->products;
            
            // 1. Procesar Node-locked (con MAC no nula)
            $nodeLockedGroups = $allProducts->whereNotNull('node_locked_host_id')
                ->filter(fn($p) => trim($p->node_locked_host_id) !== '')
                ->groupBy(function ($item) {
                    return $item->product_code . '|' . $item->node_locked_host_id;
                });

            foreach ($nodeLockedGroups as $group) {
                if ($group->count() <= 1) continue;

                $sorted = $group->sortByDesc(function ($product) {
                    return $product->expiration_date ? $product->expiration_date->timestamp : PHP_INT_MAX;
                })->values();

                $latestProduct = $sorted->first();

                foreach ($group as $product) {
                    if ($product->id !== $latestProduct->id && $product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded (MAC): ID {$product->id} - {$product->product_code}");
                    }
                }
                
                if ($latestProduct->status !== 'active') {
                    $latestProduct->status = 'active';
                    $latestProduct->save();
                }
            }

            // 2. Procesar Flotantes / Sin Host ID (Pendientes de MAC)
            $floatingProducts = $allProducts->filter(fn($p) => empty($p->node_locked_host_id))
                ->groupBy('product_code');

            foreach ($floatingProducts as $productCode => $group) {
                if ($group->count() <= 1) continue;

                $sorted = $group->sortByDesc(function ($product) {
                    return $product->expiration_date ? $product->expiration_date->timestamp : PHP_INT_MAX;
                })->values();

                $latestProduct = $sorted->first();

                foreach ($group as $product) {
                    if ($product->id !== $latestProduct->id && $product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded (Entrega Anterior Sin MAC): ID {$product->id} - {$product->product_code}");
                    }
                }

                if ($latestProduct->status !== 'active') {
                    $latestProduct->status = 'active';
                    $latestProduct->save();
                }
            }
        }

        $this->info("Revisión completada. Total de licencias marcadas como superseded: {$totalSuperseded}");
        Log::info("Comando dx:mark-superseded completado. {$totalSuperseded} licencias actualizadas.");
        
        return Command::SUCCESS;
    }
}
