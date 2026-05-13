<?php

namespace App\Jobs;

use App\Mail\WeeklyLicenseAlert;
use App\Mail\GlobalLicenseExpirationReport;
use App\Models\AlertSetting;
use App\Models\EmailLog;
use App\Services\LicenseExpirationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyLicenseAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(LicenseExpirationService $service): void
    {
        $settings = AlertSetting::first();
        
        if (!$settings || !$settings->is_active) {
            Log::info('SendWeeklyLicenseAlertsJob: El sistema de alertas está desactivado o no configurado.');
            return;
        }

        $reportData = $service->getWeeklyReportData();

        if ($reportData->isEmpty()) {
            Log::info('SendWeeklyLicenseAlertsJob: No hay licencias próximas a caducar esta semana.');
            return;
        }

        // Obtener destinatarios internos (Soporte AYS)
        $recipients = $settings->internal_emails;
        
        // Si no hay configurados, fallback al email de soporte estándar
        if (empty($recipients)) {
            $recipients = ['soporte@ats-global.com'];
        }

        try {
            // Enviar un ÚNICO reporte global con todos los clientes
            Mail::to($recipients)->send(new GlobalLicenseExpirationReport($reportData));

            Log::info('SendWeeklyLicenseAlertsJob: Reporte global enviado con éxito a ' . implode(', ', $recipients));

        } catch (\Exception $e) {
            Log::error('Error enviando reporte global de licencias: ' . $e->getMessage());
            
            // Aquí sí registramos el fallo manualmente porque el listener automático solo captura los enviados con éxito
            foreach ($recipients as $email) {
                EmailLog::create([
                    'recipient' => $email,
                    'subject' => '📊 REPORTE GLOBAL: Caducidad de Licencias',
                    'mailable_class' => GlobalLicenseExpirationReport::class,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
