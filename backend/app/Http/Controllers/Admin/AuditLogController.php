<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'activity');
        
        $stats = [
            'total_24h' => Schema::hasTable('audit_logs') ? DB::table('audit_logs')->where('created_at', '>', now()->subDay())->count() : 0,
            'errors_24h' => $this->getCombinedErrorCount(),
            'emails_24h' => Schema::hasTable('email_logs') ? DB::table('email_logs')->where('created_at', '>', now()->subDay())->count() : 0,
        ];

        $data = [
            'tab' => $tab,
            'stats' => $stats,
        ];

        switch ($tab) {
            case 'system':
                $data['logs'] = $this->getSystemLogs();
                break;
            case 'email':
                $data['logs'] = Schema::hasTable('email_logs') 
                    ? DB::table('email_logs')->orderBy('created_at', 'desc')->paginate(20)->withQueryString()
                    : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
                break;
            default:
                $data['logs'] = $this->getActivityLogs($request);
                break;
        }

        return view('admin.audit.index', $data);
    }

    private function getActivityLogs(Request $request)
    {
        if (!Schema::hasTable('audit_logs')) return collect();

        $query = DB::table('audit_logs as al')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select('al.*', 'u.name as user_name')
            ->orderBy('al.created_at', 'desc');

        if ($request->filled('level')) {
            $query->where('al.level', $request->level);
        }

        if ($request->filled('action')) {
            $query->where('al.action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('al.description', 'like', '%' . $request->search . '%')
                  ->orWhere('u.name', 'like', '%' . $request->search . '%');
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    private function getSystemLogs()
    {
        $path = storage_path('logs/laravel.log');
        if (!file_exists($path)) return [];

        try {
            $content = file_get_contents($path);
            if (!$content) return [];

            // Separar por el patrón de inicio de log: [YYYY-MM-DD HH:MM:SS]
            $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/m';
            $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            
            $logs = [];
            for ($i = 0; $i < count($parts); $i += 2) {
                if (!isset($parts[$i+1])) continue;
                
                $timestamp = $parts[$i];
                $body = $parts[$i+1];
                
                // Extraer Nivel y Mensaje
                // Formato: environment.LEVEL: Message
                preg_match('/^\s*\w+\.(\w+):\s*(.*)$/m', $body, $matches);
                
                $level = strtolower($matches[1] ?? 'info');
                $fullMessage = $matches[2] ?? $body;
                
                // Separar Mensaje de Stack Trace
                $messageParts = explode("\n", $fullMessage, 2);
                $message = trim($messageParts[0]);
                $stackTrace = isset($messageParts[1]) ? trim($messageParts[1]) : null;

                $logs[] = [
                    'timestamp' => $timestamp,
                    'level' => $level,
                    'message' => $message,
                    'stack_trace' => $stackTrace,
                    'id' => md5($timestamp . $message)
                ];
            }

            // Devolver las últimas 100 entradas para no saturar la vista
            return array_reverse(array_slice($logs, -100));
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("AuditLog: Error parsing logs: " . $e->getMessage());
            return [];
        }
    }

    public function clearActivity()
    {
        DB::table('audit_logs')->truncate();
        $this->logAction('audit_reset', 'Se ha vaciado el historial de actividad (audit_logs).');
        return back()->with('success', 'Historial de actividad reseteado.');
    }

    public function clearEmail()
    {
        if (Schema::hasTable('email_logs')) {
            DB::table('email_logs')->truncate();
            $this->logAction('clear_email_logs', 'Vaciado del historial de emails enviados.', 'info');
            return back()->with('tab', 'email')->with('success', 'Historial de emails vaciado.');
        }
        
        return back()->with('tab', 'email')->with('error', 'La tabla de logs de email no existe.');
    }

    public function clearSystem()
    {
        $path = storage_path('logs/laravel.log');
        if (file_exists($path)) {
            file_put_contents($path, '');
            $this->logAction('system_log_reset', 'Se ha vaciado el fichero de logs de sistema (laravel.log).');
            return back()->with('tab', 'system')->with('success', 'Fichero laravel.log reseteado.');
        }
        return back()->with('tab', 'system')->with('error', 'No se encontró el fichero de log.');
    }

    private function logAction($action, $description, $level = 'warning')
    {
        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'level' => $level,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }

    /**
     * Calcula el conteo de errores unificado (DB + Fichero).
     */
    private function getCombinedErrorCount()
    {
        $dbErrors = Schema::hasTable('audit_logs') 
            ? DB::table('audit_logs')->whereIn('level', ['error', 'critical', 'alert', 'emergency'])->where('created_at', '>', now()->subDay())->count() 
            : 0;

        $path = storage_path('logs/laravel.log');
        if (!file_exists($path)) return $dbErrors;

        $fileErrors = 0;
        try {
            $content = file_get_contents($path);
            // Buscamos patrones de error en las últimas 24h dentro del fichero
            // Solo contamos niveles críticos
            $yesterday = now()->subDay()->format('Y-m-d H:i:s');
            
            $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s*\w+\.(error|critical|alert|emergency):/im';
            preg_match_all($pattern, $content, $matches);
            
            if (isset($matches[1])) {
                foreach ($matches[1] as $timestamp) {
                    if ($timestamp > $yesterday) {
                        $fileErrors++;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silencio administrativo para no romper el dashboard
        }

        return $dbErrors + $fileErrors;
    }
}
