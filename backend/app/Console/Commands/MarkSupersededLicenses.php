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
    protected $signature = 'dx:mark-superseded';

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
        $daemons = LicenseInventoryDaemon::with('products')->get();
        $totalSuperseded = 0;

        foreach ($daemons as $daemon) {
            $products = $daemon->products;
            
            // Agrupar por producto y host id
            $groupedProducts = $products->groupBy(function ($product) {
                return $product->product_code . '_' . ($product->node_locked_host_id ?? 'floating');
            });

            foreach ($groupedProducts as $key => $group) {
                if ($group->count() <= 1) {
                    continue;
                }

                // Filtrar para ignorar los que ya están superseded (opcional, o procesarlos todos)
                // Encontrar el producto con la fecha de expiración más lejana
                $latestProduct = $group->sortByDesc(function ($product) {
                    // Si no hay fecha (permanente), lo tratamos como la fecha más lejana posible
                    return $product->expiration_date ? $product->expiration_date->timestamp : PHP_INT_MAX;
                })->first();

                // Marcar el resto como superseded
                foreach ($group as $product) {
                    if ($product->id !== $latestProduct->id && $product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded: ID {$product->id} - {$product->product_code}");
                    }
                }
                
                // Asegurarse de que el último está activo (si fue marcado como inactive antes)
                if ($latestProduct->status !== 'active') {
                    $latestProduct->status = 'active';
                    $latestProduct->save();
                    $this->line("Producto restaurado a active: ID {$latestProduct->id} - {$latestProduct->product_code}");
                }
            }
        }

        $this->info("Revisión completada. Total de licencias marcadas como superseded: {$totalSuperseded}");
        Log::info("Comando dx:mark-superseded completado. {$totalSuperseded} licencias actualizadas.");
        
        return Command::SUCCESS;
    }
}
