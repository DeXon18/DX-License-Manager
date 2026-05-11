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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
            'api_providers' => [
                'gemini' => $this->checkGemini(),
                'deepseek' => $this->checkDeepseek(),
                'openrouter' => $this->checkOpenRouter(),
            ],
            'security' => [
                'active_sessions' => Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0,
                'blacklist_count' => Redis::scard('jwt_blacklist') ?? 0,
                'failed_logins_24h' => Schema::hasTable('audit_log') 
                    ? DB::table('audit_log')->where('action', 'login_failed')->where('created_at', '>', now()->subDay())->count() 
                    : 0,
            ],
            'errors_24h' => Schema::hasTable('audit_log') 
                ? DB::table('audit_log')->where('level', 'error')->where('created_at', '>', now()->subDay())->count() 
                : 0,
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
        if (!$load) return ['1m' => '0.00', '5m' => '0.00', '15m' => '0.00'];
        
        return [
            '1m' => number_format($load[0], 2),
            '5m' => number_format($load[1], 2),
            '15m' => number_format($load[2], 2),
        ];
    }

    private function getMemoryMetrics()
    {
        // Try cgroup v2 (modern Proxmox)
        $memUsageFile = '/sys/fs/cgroup/memory.current';
        $memLimitFile = '/sys/fs/cgroup/memory.max';

        // Fallback to cgroup v1
        if (!file_exists($memUsageFile)) {
            $memUsageFile = '/sys/fs/cgroup/memory/memory.usage_in_bytes';
            $memLimitFile = '/sys/fs/cgroup/memory/memory.limit_in_bytes';
        }

        if (file_exists($memUsageFile) && file_exists($memLimitFile)) {
            $usedBytes = (int)trim(file_get_contents($memUsageFile));
            $limitRaw = trim(file_get_contents($memLimitFile));
            
            // If limit is "max" or a very large number, fallback to free
            if ($limitRaw !== 'max' && (int)$limitRaw < 1000000000000) {
                $totalBytes = (int)$limitRaw;
                $percent = $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0;

                return [
                    'total' => round($totalBytes / (1024 ** 3), 1) . ' GB',
                    'used' => round($usedBytes / (1024 ** 3), 1) . ' GB',
                    'percent' => $percent
                ];
            }
        }

        // Final fallback: free command
        $free = shell_exec('free -b');
        if (!$free) return ['total' => '0 GB', 'used' => '0 GB', 'percent' => 0];

        $lines = explode("\n", trim($free));
        $mem = preg_split('/\s+/', $lines[1]);
        
        $total = (int)$mem[1];
        $used = (int)$mem[2];
        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0;

        return [
            'total' => round($total / (1024 ** 3), 1) . ' GB',
            'used' => round($used / (1024 ** 3), 1) . ' GB',
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
            
            // Database Size
            $dbName = config('database.connections.mysql.database');
            $size = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $sizeMb = round($size[0]->size_mb ?? 0, 2);
            $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);

            return [
                'status' => 'online', 
                'message' => 'MariaDB Operational',
                'details' => "Size: {$sizeMb} MB · Tables: " . ($tables[0]->count ?? 0)
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Connection Failed', 'details' => $e->getMessage()];
        }
    }

    private function checkRedis()
    {
        try {
            Redis::ping();
            $info = Redis::info();
            $mem = $info['used_memory_human'] ?? 'N/A';
            
            return [
                'status' => 'online', 
                'message' => 'Redis Cache Active',
                'details' => "Memory: {$mem} · Keys: " . ($info['db0']['keys'] ?? 0)
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Service Down', 'details' => 'Not reachable'];
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
            $response = Http::timeout(5)->get("https://api.telegram.org/bot{$token}/getMe");
            return $response->successful() 
                ? ['status' => 'online', 'message' => 'Bot API Connected']
                : ['status' => 'degraded', 'message' => 'Auth Error', 'details' => 'Check Token'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'API Timeout', 'details' => Str::limit($e->getMessage(), 40)];
        }
    }

    private function checkGemini()
    {
        $key = config('ai.gemini_key');
        if (!$key) return ['status' => 'degraded', 'message' => 'Key Missing'];
        return ['status' => 'online', 'message' => 'API Active'];
    }

    private function checkDeepseek()
    {
        $key = config('ai.deepseek_key');
        if (!$key) return ['status' => 'degraded', 'message' => 'Key Missing'];
        return ['status' => 'online', 'message' => 'API Active'];
    }

    private function checkOpenRouter()
    {
        $key = config('ai.openrouter_key');
        if (!$key) return ['status' => 'degraded', 'message' => 'Key Missing'];
        return ['status' => 'online', 'message' => 'API Active'];
    }
}
