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
            
            // Ya no agrupamos por MAC para marcar como 'superseded' porque las renovaciones futuras coexisten
            foreach ($allProducts as $product) {
                // Solo caducamos licencias cuyo expiration_date ya pasó
                if ($product->expiration_date && $product->expiration_date->format('Y') !== '9999' && $product->expiration_date->isPast()) {
                    if ($product->status !== 'superseded') {
                        $product->status = 'superseded';
                        $product->save();
                        $totalSuperseded++;
                        $this->line("Producto marcado como superseded por caducidad: ID {$product->id} - {$product->product_code}");
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
