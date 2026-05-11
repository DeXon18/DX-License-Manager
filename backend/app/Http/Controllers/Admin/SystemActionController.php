<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class SystemActionController extends Controller
{
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            $this->logAction('cache_clear', 'Manual cache and view clearing');

            return response()->json(['success' => true, 'message' => 'Caché del sistema limpiada con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function restartQueues()
    {
        try {
            Artisan::call('queue:restart');
            $this->logAction('queue_restart', 'Manual queue workers restart signal sent');

            return response()->json(['success' => true, 'message' => 'Señal de reinicio enviada a los workers.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function backupDatabase()
    {
        try {
            // Ejecutar script de backup
            // Nota: El script debe tener permisos de ejecución
            $process = Process::run('/var/www/html/scripts/backup-db.sh beta');
            
            if ($process->successful()) {
                $this->logAction('db_backup', 'Manual database backup created');
                return response()->json(['success' => true, 'message' => 'Backup generado correctamente.']);
            }

            return response()->json(['success' => false, 'message' => 'Error en el script: ' . $process->errorOutput()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function toggleMaintenance(Request $request)
    {
        try {
            $isDown = app()->isDownForMaintenance();
            
            if ($isDown) {
                Artisan::call('up');
                $this->logAction('maintenance_off', 'System brought back online');
                return response()->json(['success' => true, 'message' => 'Sistema ONLINE.']);
            } else {
                Artisan::call('down', [
                    '--secret' => config('app.maintenance_secret', 'dx-dev-access'),
                    '--refresh' => 15
                ]);
                $this->logAction('maintenance_on', 'System put into maintenance mode');
                return response()->json(['success' => true, 'message' => 'Sistema en MANTENIMIENTO.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function testTelegram()
    {
        try {
            $token = config('services.telegram-bot-api.token');
            $chatId = config('services.telegram-bot-api.chat_id') ?? '677271032'; // Fallback a Oskar si no hay chat_id pro

            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "🔔 *DX NOC Test*\nPrueba de conectividad desde el Dashboard.\n🕒 " . now()->format('H:i:s'),
                'parse_mode' => 'Markdown'
            ]);

            if ($response->successful()) {
                $this->logAction('telegram_test', 'Manual telegram notification test sent');
                return response()->json(['success' => true, 'message' => 'Notificación de prueba enviada.']);
            }

            return response()->json(['success' => false, 'message' => 'Error de Telegram: ' . $response->body()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function logAction($action, $description)
    {
        DB::table('audit_log')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'level' => 'warning', // Las acciones de sistema suelen ser importantes
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}
