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
        $totalPromptTokensAllTime = AiTokenLog::sum('prompt_tokens');
        $totalCompletionTokensAllTime = AiTokenLog::sum('completion_tokens');

        // Estimación de coste (Gemini Flash 1.5 aprox: $0.15/1M Prompt, $0.60/1M Completion)
        $totalCostThisMonth = ($promptTokensThisMonth / 1000000 * 0.15) + ($completionTokensThisMonth / 1000000 * 0.60);
        $totalCostAllTime = ($totalPromptTokensAllTime / 1000000 * 0.15) + ($totalCompletionTokensAllTime / 1000000 * 0.60);

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

        // 4. Uso por Usuario (Mes Actual)
        $userStats = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('SUM(total_tokens) as total_tokens'), DB::raw('COUNT(*) as requests_count'))
            ->groupBy('user_id')
            ->with('user:id,name,email')
            ->orderByDesc('total_tokens')
            ->get();

        // 4. Uso diario (Mes Actual) para la gráfica, agrupado por fecha y proveedor
        $dailyRecords = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->select(DB::raw('DATE(created_at) as date'), 'provider', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'provider')
            ->orderBy('date')
            ->get();

        // Estructurar para el frontend: ['2026-05-22' => ['gemini' => 120, 'deepseek' => 40]]
        $dailyStats = [];
        $providersSet = [];
        foreach ($dailyRecords as $record) {
            if (!isset($dailyStats[$record->date])) {
                $dailyStats[$record->date] = [];
            }
            $dailyStats[$record->date][$record->provider] = $record->total;
            $providersSet[$record->provider] = true;
        }

        $chartData = [
            'dates' => array_keys($dailyStats),
            'providers' => array_keys($providersSet),
            'stats' => $dailyStats
        ];

        // 5. Historial reciente con paginación
        $logs = AiTokenLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.system.ai-costs', compact(
            'totalTokensThisMonth',
            'promptTokensThisMonth',
            'completionTokensThisMonth',
            'totalTokensAllTime',
            'totalCostThisMonth',
            'totalCostAllTime',
            'providerStats',
            'actionStats',
            'userStats',
            'chartData',
            'logs'
        ));
    }
}
