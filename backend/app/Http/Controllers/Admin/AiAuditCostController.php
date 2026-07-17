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
        $currentMonth = Carbon::now()->subDays(29)->startOfDay(); // Últimos 30 días en lugar del mes natural

        // 1. Estadísticas Globales
        $totalTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('total_tokens');
        $promptTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('prompt_tokens');
        $completionTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('completion_tokens');
        
        $totalTokensAllTime = AiTokenLog::sum('total_tokens');
        $totalPromptTokensAllTime = AiTokenLog::sum('prompt_tokens');
        $totalCompletionTokensAllTime = AiTokenLog::sum('completion_tokens');

        // Cálculo dinámico de coste por modelo basado en BD (AiModel)
        $modelsFromDb = \App\Models\AiModel::all()->keyBy('openrouter_id');
        
        $calculateCostByModel = function($query) use ($modelsFromDb) {
            $modelStats = $query->select('model', DB::raw('SUM(prompt_tokens) as prompt_tokens'), DB::raw('SUM(completion_tokens) as completion_tokens'))
                ->groupBy('model')
                ->get();
            
            $cost = 0;
            foreach ($modelStats as $stat) {
                $model = $stat->model ?? 'default';
                $promptPrice = 0;
                $completionPrice = 0;
                
                if (isset($modelsFromDb[$model])) {
                    $promptPrice = $modelsFromDb[$model]->price_prompt;
                    $completionPrice = $modelsFromDb[$model]->price_completion;
                } else {
                    // Fallback para logs legacy (ej. "deepseek-chat", "gemini-3.1-flash-lite")
                    $matched = $modelsFromDb->first(function($dbModel) use ($model) {
                        $shortName = explode('/', $dbModel->openrouter_id)[1] ?? $dbModel->openrouter_id;
                        return str_contains($dbModel->openrouter_id, $model) || str_contains($model, $shortName);
                    });
                    if ($matched) {
                        $promptPrice = $matched->price_prompt;
                        $completionPrice = $matched->price_completion;
                    }
                }

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
            ->select('user_id', DB::raw('SUM(total_tokens) as total_tokens'), DB::raw('COUNT(*) as requests_count'))
            ->groupBy('user_id')
            ->with('user:id,name,email')
            ->orderByDesc('total_tokens')
            ->get();

        // 5. Uso diario (Mes Actual) para la gráfica, agrupado por fecha y proveedor
        $dailyRecords = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->select(DB::raw('DATE(created_at) as date'), 'provider', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'provider')
            ->orderBy('date')
            ->get();

        $dailyStats = [];
        $providersSet = [];
        
        // Pre-fill days from start of month to today
        $today = Carbon::today();
        $currentDate = $currentMonth->copy();
        while ($currentDate <= $today) {
            $dailyStats[$currentDate->format('Y-m-d')] = [];
            $currentDate->addDay();
        }

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

        // 6. Uso diario (Mes Actual) agrupado por fecha y usuario
        $dailyUserRecords = AiTokenLog::with('user:id,name')
            ->where('created_at', '>=', $currentMonth)
            ->select(DB::raw('DATE(created_at) as date'), 'user_id', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'user_id')
            ->orderBy('date')
            ->get();

        $dailyUserStats = [];
        $usersSet = [];
        
        $currentDate = $currentMonth->copy();
        while ($currentDate <= $today) {
            $dailyUserStats[$currentDate->format('Y-m-d')] = [];
            $currentDate->addDay();
        }

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

        // 7. Uso por proveedor (Últimos 7 días)
        $startOfWeek = Carbon::today()->subDays(6);
        $weeklyRecords = AiTokenLog::where('created_at', '>=', $startOfWeek)
            ->select(DB::raw('DATE(created_at) as date'), 'provider', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'provider')
            ->orderBy('date')
            ->get();

        $weeklyStats = [];
        $weeklyProvidersSet = [];
        // Pre-fill dates for the last 7 days
        $currentDate = $startOfWeek->copy();
        while ($currentDate <= $today) {
            $weeklyStats[$currentDate->format('Y-m-d')] = [];
            $currentDate->addDay();
        }

        foreach ($weeklyRecords as $record) {
            $weeklyStats[$record->date][$record->provider] = $record->total;
            $weeklyProvidersSet[$record->provider] = true;
        }

        $weeklyChartData = [
            'dates' => array_keys($weeklyStats),
            'providers' => array_keys($weeklyProvidersSet),
            'stats' => $weeklyStats
        ];

        // 8. Uso por usuario (Últimos 7 días)
        $weeklyUserRecords = AiTokenLog::with('user:id,name')
            ->where('created_at', '>=', $startOfWeek)
            ->select(DB::raw('DATE(created_at) as date'), 'user_id', DB::raw('SUM(total_tokens) as total'))
            ->groupBy(DB::raw('DATE(created_at)'), 'user_id')
            ->orderBy('date')
            ->get();

        $weeklyUserStats = [];
        $weeklyUsersSet = [];
        $currentDate = $startOfWeek->copy();
        while ($currentDate <= $today) {
            $weeklyUserStats[$currentDate->format('Y-m-d')] = [];
            $currentDate->addDay();
        }

        foreach ($weeklyUserRecords as $record) {
            $userName = $record->user->name ?? 'Sistema';
            $weeklyUserStats[$record->date][$userName] = $record->total;
            $weeklyUsersSet[$userName] = true;
        }

        $weeklyUserChartData = [
            'dates' => array_keys($weeklyUserStats),
            'users' => array_keys($weeklyUsersSet),
            'stats' => $weeklyUserStats
        ];

        // 8. Historial reciente con paginación
        $logs = AiTokenLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(15);

        // 9. Estadísticas de fallos (Mes Actual)
        $rawFailureStats = AiTokenLog::where('created_at', '>=', $currentMonth)
            ->where('status', 'failed')
            ->select('model', 'error_message', DB::raw('COUNT(*) as error_count'))
            ->groupBy('model', 'error_message')
            ->orderByDesc('error_count')
            ->get();

        // Agrupar mensajes equivalentes con distintos prefijos (ej: "Status 404:" vs "Fallo en API... (Status 404)")
        $groupedFailures = [];
        foreach ($rawFailureStats as $stat) {
            $msg = $stat->error_message;
            if (preg_match('/(?:Status |\(Status )(\d+)[):]\s*(.*)/', $msg, $matches)) {
                $msg = "Error " . $matches[1] . ": " . $matches[2];
            }
            
            $key = $stat->model . '|' . $msg;
            if (!isset($groupedFailures[$key])) {
                $groupedFailures[$key] = (object)[
                    'model' => $stat->model,
                    'error_message' => $msg,
                    'error_count' => 0
                ];
            }
            $groupedFailures[$key]->error_count += $stat->error_count;
        }
        
        $failureStats = collect(array_values($groupedFailures))->sortByDesc('error_count')->values();

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
            'weeklyChartData',
            'weeklyUserChartData',
            'failureStats',
            'logs',
            'modelsFromDb'
        ));
    }
}
