<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiTokenLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiAuditCostController extends Controller
{
    /**
     * Muestra el panel de monitorización de costes y tokens de IA.
     */
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // 1. Estadísticas Globales
        $totalTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('total_tokens');
        $promptTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('prompt_tokens');
        $completionTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('completion_tokens');
        
        $totalTokensAllTime = AiTokenLog::sum('total_tokens');

        // 2. Uso por Proveedor (Mes Actual)
        $providerStats = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->select('provider', DB::raw('SUM(total_tokens) as total_tokens'), DB::raw('COUNT(*) as requests_count'))
            ->groupBy('provider')
            ->orderByDesc('total_tokens')
            ->get();

        // 3. Uso por Acción (Mes Actual)
        $actionStats = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->select('action', DB::raw('SUM(total_tokens) as total_tokens'), DB::raw('COUNT(*) as requests_count'))
            ->groupBy('action')
            ->orderByDesc('total_tokens')
            ->get();

        // 4. Historial reciente con paginación
        $logs = AiTokenLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.system.ai-costs', compact(
            'totalTokensThisMonth',
            'promptTokensThisMonth',
            'completionTokensThisMonth',
            'totalTokensAllTime',
            'providerStats',
            'actionStats',
            'logs'
        ));
    }
}
