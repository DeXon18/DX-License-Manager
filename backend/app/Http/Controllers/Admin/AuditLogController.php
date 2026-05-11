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
        if (!Schema::hasTable('audit_logs')) {
            return view('admin.audit.index', ['logs' => collect(), 'stats' => []]);
        }

        $query = DB::table('audit_logs as al')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select('al.*', 'u.name as user_name')
            ->orderBy('al.created_at', 'desc');

        // Filtros
        if ($request->has('level') && $request->level != '') {
            $query->where('al.level', $request->level);
        }

        if ($request->has('action') && $request->action != '') {
            $query->where('al.action', 'like', '%' . $request->action . '%');
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('al.description', 'like', '%' . $request->search . '%')
                  ->orWhere('u.name', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Estadísticas simples para el header
        $stats = [
            'total_24h' => DB::table('audit_logs')->where('created_at', '>', now()->subDay())->count(),
            'errors_24h' => DB::table('audit_logs')->where('level', 'error')->where('created_at', '>', now()->subDay())->count(),
            'critical_actions' => DB::table('audit_logs')->whereIn('action', ['db_backup', 'maintenance_on', 'db_delete'])->count(),
        ];

        return view('admin.audit.index', compact('logs', 'stats'));
    }
}
