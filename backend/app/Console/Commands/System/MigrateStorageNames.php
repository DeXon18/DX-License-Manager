<?php

namespace App\Console\Commands\System;

use App\Services\System\StorageNormalizationService;
use Illuminate\Console\Command;

class MigrateStorageNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:migrate-storage-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra los nombres de las carpetas de licencias de slug a MAYÚSCULAS sin puntos ni comas';

    /**
     * Execute the console command.
     */
    public function handle(StorageNormalizationService $normalizationService)
    {
        $this->info('Iniciando migración de nombres de carpetas de almacenamiento...');

        $results = $normalizationService->migrateExistingFolders();

        $this->table(
            ['Categoría', 'Cantidad'],
            [
                ['Migradas', $results['migrated']],
                ['Omitidas', $results['skipped']],
                ['Errores', count($results['errors'])],
            ]
        );

        if (!empty($results['errors'])) {
            $this->error('Se produjeron errores durante la migración:');
            foreach ($results['errors'] as $error) {
                $this->line("- {$error}");
            }
        } else {
            $this->success('Migración completada con éxito.');
        }

        return 0;
    }

    /**
     * Helper para mostrar mensaje de éxito
     */
    protected function success($message)
    {
        $this->line("<info>{$message}</info>");
    }
}
