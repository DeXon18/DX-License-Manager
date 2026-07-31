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
                $sorted = $group->sortByDesc(function ($product) {
                    return $product->expiration_date ? $product->expiration_date->timestamp : PHP_INT_MAX;
                })->values();

                $latestProduct = $sorted->first();

                // El último siempre activo a menos que haya caducado
                if ($latestProduct->expiration_date && $latestProduct->expiration_date->isPast()) {
                    if ($latestProduct->status !== 'superseded') {
                        $latestProduct->status = 'superseded';
                        $latestProduct->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded por caducidad (MAC): ID {$latestProduct->id} - {$latestProduct->product_code}");
                    }
                } else {
                    if ($latestProduct->status !== 'active') {
                        $latestProduct->status = 'active';
                        $latestProduct->save();
                    }
                }

                // Los demás (antiguos) son superseded siempre
                for ($i = 1; $i < $sorted->count(); $i++) {
                    $product = $sorted[$i];
                    if ($product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded por reemplazo (MAC): ID {$product->id} - {$product->product_code}");
                    }
                }
            }

            // 2. Procesar Flotantes / Sin Host ID (Paquetes aditivos)
            $floatingProducts = $allProducts->filter(fn($p) => empty($p->node_locked_host_id));

            foreach ($floatingProducts as $product) {
                if ($product->expiration_date && $product->expiration_date->isPast()) {
                    if ($product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto flotante marcado como superseded por caducidad: ID {$product->id} - {$product->product_code}");
                    }
                } else {
                    if ($product->status !== 'active') {
                        $product->status = 'active';
                        $product->save();
                    }
                }
            }
        }

        $this->info("Revisión completada. Total de licencias marcadas como superseded: {$totalSuperseded}");
        Log::info("Comando dx:mark-superseded completado. {$totalSuperseded} licencias actualizadas.");
        
        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
