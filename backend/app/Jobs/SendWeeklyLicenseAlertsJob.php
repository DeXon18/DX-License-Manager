<?php

namespace App\Jobs;

use App\Mail\WeeklyLicenseAlert;
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
        $settings = AlertSetting::where('is_active', true)->first();
        
        if (!$settings) {
            Log::warning('SendWeeklyLicenseAlertsJob: No se encontró configuración de alertas activa.');
            return;
        }

        $reportData = $service->getWeeklyReportData();

        if ($reportData->isEmpty()) {
            Log::info('SendWeeklyLicenseAlertsJob: No hay licencias próximas a caducar esta semana.');
            return;
        }

        foreach ($reportData as $data) {
            $client = $data['client'];
            $expiring = $data['expiring'];
            $recipients = $data['recipients'];

            foreach ($recipients as $contact) {
                try {
                    $mailable = new WeeklyLicenseAlert($client, $expiring);
                    
                    // Send to contact
                    Mail::to($contact->email)->send($mailable);

                    // Log success
                    EmailLog::create([
                        'recipient' => $contact->email,
                        'subject' => '📅 Reporte Semanal de Caducidad de Licencias — ' . $client->name,
                        'mailable_class' => WeeklyLicenseAlert::class,
                        'status' => 'sent',
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error enviando alerta semanal a ' . $contact->email . ': ' . $e->getMessage());
                    
                    EmailLog::create([
                        'recipient' => $contact->email,
                        'subject' => '📅 Reporte Semanal de Caducidad de Licencias — ' . $client->name,
                        'mailable_class' => WeeklyLicenseAlert::class,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            // Send internal copies if configured
            $internalEmails = $settings->internal_emails;
            if (!empty($internalEmails)) {
                foreach ($internalEmails as $internalEmail) {
                    try {
                        Mail::to($internalEmail)->send(new WeeklyLicenseAlert($client, $expiring));
                    } catch (\Exception $e) {
                        Log::error('Error enviando copia interna de alerta a ' . $internalEmail . ': ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
