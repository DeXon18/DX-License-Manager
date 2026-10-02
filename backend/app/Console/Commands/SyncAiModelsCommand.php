<?php

namespace App\Console\Commands;

use App\Services\AI\OpenRouterSyncService;
use Illuminate\Console\Command;

class SyncAiModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:sync-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los modelos y precios con la API pública de OpenRouter y detecta modelos deprecados';

    /**
     * Execute the console command.
     */
    public function handle(OpenRouterSyncService $syncService): int
    {
        $this->info("Iniciando sincronización con OpenRouter...");

        $result = $syncService->sync();

        if (!$result['success']) {
            $this->error($result['message']);
            return Command::FAILURE;
        }

        $this->info("Total modelos remotos en OpenRouter: " . $result['total_remote']);
        $this->info("Modelos locales actualizados: " . $result['updated']);

        if ($result['deprecated'] > 0) {
            $this->warn("Modelos descatalogados/inactivados: " . $result['deprecated']);
        }

        if (!empty($result['broken_routes'])) {
            $this->error("¡ATENCIÓN! Se han detectado rutas de IA configuradas con modelos descatalogados:");
            foreach ($result['broken_routes'] as $br) {
                $this->line("  - Tarea: [{$br['task']}] ({$br['type']}) -> Modelo: {$br['model_name']}");
            }
        }

        $this->info("Sincronización completada con éxito.");
        return Command::SUCCESS;
    }
}
