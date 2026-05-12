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
            'errors_24h' => Schema::hasTable('audit_logs') ? DB::table('audit_logs')->where('level', 'error')->where('created_at', '>', now()->subDay())->count() : 0,
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
                $data['logs'] = DB::table('email_logs')->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
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
        if (!file_exists($path)) return "Archivo laravel.log no encontrado.";

        try {
            $file = new \SplFileObject($path, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            $start = max(0, $totalLines - 200);
            $file->seek($start);
            
            $content = "";
            while (!$file->eof()) {
                $content .= $file->current();
                $file->next();
            }
            
            return $content ?: "Archivo vacío.";
        } catch (\Exception $e) {
            return "Error al leer el archivo: " . $e->getMessage();
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
        DB::table('email_logs')->truncate();
        $this->logAction('email_reset', 'Se ha vaciado el historial de correos (email_logs).');
        return back()->with('tab', 'email')->with('success', 'Historial de emails reseteado.');
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

    private function logAction($action, $description)
    {
        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'level' => 'warning',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}
