<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use App\Models\Contract;
use App\Models\AiAuditResult;
use App\Models\Client;
use Carbon\Carbon;

class SystemDashboardController extends Controller
{
    /**
     * Display the system dashboard.
     */
    public function index()
    {
        $metrics = [
            'os' => [
                'name' => PHP_OS,
                'php_version' => PHP_VERSION,
                'uptime' => $this->getUptime(),
                'load' => $this->getLoadAverage(),
            ],
            'hardware' => [
                'memory' => $this->getMemoryMetrics(),
                'disk' => $this->getDiskMetrics(),
            ],
            'services' => [
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
                'n8n' => $this->checkN8n(),
                'telegram' => $this->checkTelegram(),
            ],
            'business' => [
                'total_contracts' => Contract::count(),
                'expiring_soon' => Contract::where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays(30))
                    ->count(),
                'total_clients' => Client::count(),
                'total_audits' => AiAuditResult::count(),
                'pending_audits' => AiAuditResult::where('status', 'processing')->count(),
                'failed_audits' => AiAuditResult::where('status', 'failed')->count(),
            ],
            'trends' => $this->getAuditTrendData(),
            'distribution' => $this->getDaemonDistribution(),
        ];

        return view('admin.system.dashboard', compact('metrics'));
    }

    private function getUptime()
    {
        $uptime = shell_exec('uptime');
        return trim($uptime ?? 'N/A');
    }

    private function getLoadAverage()
    {
        $load = sys_getloadavg();
        return $load ? implode(' / ', $load) : 'N/A';
    }

    private function getMemoryMetrics()
    {
        $free = shell_exec('free -m');
        if (!$free) return ['total' => 0, 'used' => 0, 'percent' => 0];

        $lines = explode("\n", trim($free));
        $mem = preg_split('/\s+/', $lines[1]);
        
        $total = (int)$mem[1];
        $used = (int)$mem[2];
        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0;

        return [
            'total' => round($total / 1024, 1) . ' GB',
            'used' => round($used / 1024, 1) . ' GB',
            'percent' => $percent
        ];
    }

    private function getDiskMetrics()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0;

        return [
            'total' => round($total / (1024 ** 3), 1) . ' GB',
            'used' => round($used / (1024 ** 3), 1) . ' GB',
            'percent' => $percent
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'online', 'message' => 'MariaDB Operational'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Connection Failed'];
        }
    }

    private function checkRedis()
    {
        try {
            Redis::ping();
            return ['status' => 'online', 'message' => 'Redis Cache Active'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Service Down'];
        }
    }

    private function checkN8n()
    {
        $url = config('ai.n8n_webhook_url');
        if (!$url) return ['status' => 'degraded', 'message' => 'URL Not Configured'];

        try {
            // Just a check to the base URL or health endpoint if exists
            $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
            $response = Http::timeout(2)->get($baseUrl . '/healthz');
            
            return $response->successful() 
                ? ['status' => 'online', 'message' => 'n8n Engine Ready']
                : ['status' => 'degraded', 'message' => 'n8n Unresponsive'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'n8n Unreachable'];
        }
    }

    private function checkTelegram()
    {
        $token = config('services.telegram-bot-api.token');
        if (!$token) return ['status' => 'degraded', 'message' => 'Token Missing'];

        try {
            $response = Http::timeout(2)->get("https://api.telegram.org/bot{$token}/getMe");
            return $response->successful() 
                ? ['status' => 'online', 'message' => 'Bot API Connected']
                : ['status' => 'degraded', 'message' => 'Auth Error'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'API Timeout'];
        }
    }

    private function getAuditTrendData()
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days->put($date, [
                'label' => now()->subDays($i)->format('d M'),
                'count' => 0
            ]);
        }

        $audits = AiAuditResult::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->groupBy('date')
            ->get();

        foreach ($audits as $audit) {
            if ($days->has($audit->date)) {
                $days->get($audit->date)['count'] = $audit->aggregate;
            }
        }

        return [
            'labels' => $days->pluck('label')->toArray(),
            'data' => $days->pluck('count')->toArray(),
        ];
    }

    private function getDaemonDistribution()
    {
        // Distribución simple basada en AiAuditResult (metadatos extraídos)
        // O basándonos en la tabla de inventario si está poblada
        $data = \App\Models\LicenseInventoryDaemon::select('vendor_daemon', DB::raw('count(*) as total'))
            ->groupBy('vendor_daemon')
            ->pluck('total', 'vendor_daemon')
            ->toArray();

        return [
            'labels' => array_keys($data),
            'values' => array_values($data),
        ];
    }
}
