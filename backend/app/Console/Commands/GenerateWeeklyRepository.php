<?php

namespace App\Console\Commands;

use App\Services\Licensing\LicenseRepositoryService;
use App\Mail\WeeklyLicenseReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GenerateWeeklyRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:archive-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el ZIP semanal de licencias y lo envía por correo a soporte';

    /**
     * Execute the console command.
     */
    public function handle(LicenseRepositoryService $repositoryService)
    {
        $this->info('Iniciando generación de repositorio semanal...');

        try {
            $archive = $repositoryService->generateWeeklyArchive();

            if (!$archive) {
                $this->warn('No se encontraron licencias nuevas para archivar esta semana.');
                return 0;
            }

            $this->info("Archivo generado: {$archive->filename}");
            $this->info("Total de archivos incluidos: {$archive->files_count}");

            // Envío de correo (Sólo en Producción)
            if (app()->environment('production')) {
                $recipient = config('mail.support_address', 'Soporte@ats-global.com');
                $this->info("Enviando correo a: {$recipient}...");
                
                Mail::to($recipient)->send(new WeeklyLicenseReport($archive));

                $this->info('✅ Repositorio semanal completado y enviado con éxito.');
            } else {
                $this->info('✅ Repositorio semanal generado. Envío omitido (no es producción).');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la generación del repositorio: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
