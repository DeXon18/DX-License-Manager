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

        // Cálculo dinámico de coste por modelo
        $pricingConfig = config('ai.pricing', []);
        
        $calculateCostByModel = function($query) use ($pricingConfig) {
            $modelStats = $query->select('model', DB::raw('SUM(prompt_tokens) as prompt_tokens'), DB::raw('SUM(completion_tokens) as completion_tokens'))
                ->groupBy('model')
                ->get();
            
            $cost = 0;
            foreach ($modelStats as $stat) {
                $model = $stat->model ?? 'default';
                $modelKey = 'default';
                
                if (isset($pricingConfig[$model])) {
                    $modelKey = $model;
                } elseif (str_contains(strtolower($model), 'free')) {
                    $modelKey = 'default';
                } elseif (str_contains(strtolower($model), 'gemini')) {
                    $modelKey = 'gemini-1.5-flash'; // Fallback
                } elseif (str_contains(strtolower($model), 'deepseek')) {
                    $modelKey = 'deepseek-chat'; // Fallback
                }

                $promptPrice = $pricingConfig[$modelKey]['prompt'] ?? 0;
                $completionPrice = $pricingConfig[$modelKey]['completion'] ?? 0;

                $cost += ($stat->prompt_tokens / 1000000 * $promptPrice) + ($stat->completion_tokens / 1000000 * $completionPrice);
            }
            return $cost;
        };

        $totalCostThisMonth = $calculateCostByModel(AiTokenLog::where('created_at', '>=', $currentMonth));
        $totalCostAllTime = $calculateCostByModel(AiTokenLog::query());

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

        // 5. Uso diario (Mes Actual) agrupado por fecha y usuario
        $dailyUserRecords = AiTokenLog::with('user:id,name')
            ->where('created_at', '>=', $currentMonth)
            ->whereNotNull('user_id')
            ->select(DB::raw('DATE(created_at) as date'), 'user_id', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'user_id')
            ->orderBy('date')
            ->get();

        $dailyUserStats = [];
        $usersSet = [];
        foreach ($dailyUserRecords as $record) {
            if (!isset($dailyUserStats[$record->date])) {
                $dailyUserStats[$record->date] = [];
            }
            $userName = $record->user->name ?? 'Sistema';
            $dailyUserStats[$record->date][$userName] = $record->total;
            $usersSet[$userName] = true;
        }

        $userChartData = [
            'dates' => array_keys($dailyUserStats),
            'users' => array_keys($usersSet),
            'stats' => $dailyUserStats
        ];

        // 6. Uso horario (Día Actual) agrupado por hora y proveedor
        $today = Carbon::today();
        $hourlyRecords = AiTokenLog::where('created_at', '>=', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), 'provider', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('HOUR(created_at)'), 'provider')
            ->orderBy('hour')
            ->get();

        $hourlyStats = [];
        $hourlyProvidersSet = [];
        // Pre-fill hours 0 to 23
        for ($i = 0; $i <= 23; $i++) {
            $h = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hourlyStats[$h] = [];
        }

        foreach ($hourlyRecords as $record) {
            $h = str_pad($record->hour, 2, '0', STR_PAD_LEFT) . ':00';
            $hourlyStats[$h][$record->provider] = $record->total;
            $hourlyProvidersSet[$record->provider] = true;
        }

        $hourlyChartData = [
            'hours' => array_keys($hourlyStats),
            'providers' => array_keys($hourlyProvidersSet),
            'stats' => $hourlyStats
        ];

        // 7. Uso horario (Día Actual) agrupado por hora y usuario
        $hourlyUserRecords = AiTokenLog::with('user:id,name')
            ->where('created_at', '>=', $today)
            ->whereNotNull('user_id')
            ->select(DB::raw('HOUR(created_at) as hour'), 'user_id', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('HOUR(created_at)'), 'user_id')
            ->orderBy('hour')
            ->get();

        $hourlyUserStats = [];
        $hourlyUsersSet = [];
        for ($i = 0; $i <= 23; $i++) {
            $h = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hourlyUserStats[$h] = [];
        }

        foreach ($hourlyUserRecords as $record) {
            $h = str_pad($record->hour, 2, '0', STR_PAD_LEFT) . ':00';
            $userName = $record->user->name ?? 'Sistema';
            $hourlyUserStats[$h][$userName] = $record->total;
            $hourlyUsersSet[$userName] = true;
        }

        $hourlyUserChartData = [
            'hours' => array_keys($hourlyUserStats),
            'users' => array_keys($hourlyUsersSet),
            'stats' => $hourlyUserStats
        ];

        // 8. Historial reciente con paginación
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
            'userChartData',
            'hourlyChartData',
            'hourlyUserChartData',
            'logs'
        ));
    }
}
