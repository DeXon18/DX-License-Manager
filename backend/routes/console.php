<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- Fase 13: Alertas y Notificaciones ---

use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendWeeklyLicenseAlertsJob;

// Envío semanal todos los lunes a las 07:30 AM
Schedule::job(new SendWeeklyLicenseAlertsJob)->mondays()->at('07:30');

// Comando manual para pruebas
Artisan::command('dx:send-weekly-alerts', function () {
    $this->info('Iniciando envío manual de alertas semanales (síncrono)...');
    SendWeeklyLicenseAlertsJob::dispatchSync();
    $this->info('Alertas procesadas y enviadas correctamente.');
})->purpose('Enviar manualmente el reporte semanal de caducidad de licencias');

// Comprobación diaria de presupuesto de tokens IA
Schedule::command('ai:check-budget')->dailyAt('23:55');
