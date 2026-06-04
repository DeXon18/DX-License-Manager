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
                'maintenance' => file_exists(storage_path('framework/maintenance_selective')),
            ],
            'hardware' => [
                'memory' => $this->getMemoryMetrics(),
                'disk' => $this->getDiskMetrics(),
                'network' => $this->getNetworkMetrics(),
            ],
            'git' => $this->getGitMetrics(),
            'services' => [
                'Infraestructura Base' => [
                    'database' => array_merge($this->checkDatabase(), [
                        'label' => 'Database Monitor (MariaDB)',
                        'url' => route('admin.system.database')
                    ]),
                    'redis' => array_merge($this->checkRedis(), [
                        'label' => 'Queue Monitor (Redis)',
                        'url' => route('admin.queue-monitor.index')
                    ]),
                    'docker' => [
                        'status' => 'online',
                        'icon' => 'server',
                        'label' => 'Docker Monitor',
                        'message' => 'Salud de contenedores',
                        'details' => 'Telemetría de CPU/RAM',
                        'url' => route('admin.system.docker')
                    ],
                ],
                'Seguridad y Trazabilidad' => [
                    'audit' => [
                        'status' => 'online',
                        'icon' => 'shield',
                        'label' => 'Auditoría y Logs',
                        'message' => 'Trazabilidad total',
                        'details' => 'Filtros por IP y acción',
                        'url' => route('admin.audit.index')
                    ],
                    'backups' => [
                        'status' => 'online',
                        'icon' => 'archive',
                        'label' => 'Gestión de Backups',
                        'message' => 'Historial completo',
                        'details' => 'Descargas y espacio',
                        'url' => route('admin.backups.index')
                    ],
                ],
                'Procesadores y Alertas' => [
                    'n8n' => $this->checkN8n(),
                    'telegram' => $this->checkTelegram(),
                ],
                'Inteligencia Artificial' => [
                    'openrouter_core' => $this->checkOpenRouterCore(),
                    'ai_routing' => array_merge($this->checkAiRouting(), [
                        'label' => 'AI Routing Hub',
                        'url' => route('admin.system.ai-routing.index')
                    ]),
                    'ai_costs' => [
                        'status' => 'online',
                        'icon' => 'chart',
                        'label' => 'Costes IA',
                        'message' => 'Monitorización de tokens',
                        'details' => 'Telemetría de motores',
                        'url' => route('admin.system.ai-costs')
                    ],
                ],
            ],
            'security' => [
                'active_sessions' => $this->getActiveSessionsCount(),
                'blacklist_count' => (int) Redis::zcount('jwt_blacklist', time(), '+inf'),
                'failed_logins_24h' => Schema::hasTable('audit_logs') 
                    ? DB::table('audit_logs')->where('action', 'login_failed')->where('created_at', '>', now()->subDay())->count() 
                    : 0,
            ],
            'errors_24h' => Schema::hasTable('audit_logs') 
                ? DB::table('audit_logs')->whereIn('level', ['error', 'critical'])->where('created_at', '>', now()->subDay())->count() 
                : 0,
        ];

        return view('admin.system.dashboard', compact('metrics'));
    }

    /**
     * Display the dedicated docker monitor page.
     */
    public function docker(\App\Services\DockerMonitorService $dockerMonitor)
    {
        $containers = $dockerMonitor->getContainers();
        return view('admin.system.docker', compact('containers'));
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

        $pathStorage = storage_path();
        $pathLogs = storage_path('logs');
        
        $sizeStorage = file_exists($pathStorage) ? (int) shell_exec("du -sb {$pathStorage} | cut -f1") : 0;
        $sizeLogs = file_exists($pathLogs) ? (int) shell_exec("du -sb {$pathLogs} | cut -f1") : 0;

        return [
            'total' => round($total / (1024 ** 3), 1) . ' GB',
            'used' => round($used / (1024 ** 3), 1) . ' GB',
            'percent' => $percent,
            'folders' => [
                'storage' => $this->formatBytes($sizeStorage),
                'logs' => $this->formatBytes($sizeLogs),
                'total' => $this->formatBytes($sizeStorage),
            ]
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            
            $dbName = config('database.connections.mysql.database');
            $size = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $sizeMb = number_format($size[0]->size ?? 0, 2);
            
            $tables = DB::select("SELECT count(*) as count FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            
            // Threads and Slow Queries
            $status = DB::select("SHOW STATUS WHERE Variable_name IN ('Threads_connected', 'Slow_queries')");
            $statusMap = collect($status)->pluck('Value', 'Variable_name');

            return [
                'status' => 'online', 
                'icon' => 'database',
                'label' => 'MariaDB',
                'message' => 'Operacional',
                'details' => "{$sizeMb}MB · {$tables[0]->count} Tablas",
                'extra' => [
                    'threads' => $statusMap->get('Threads_connected', 0),
                    'slow_queries' => $statusMap->get('Slow_queries', 0),
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'icon' => 'database', 'label' => 'MariaDB', 'message' => 'Error de Conexión', 'details' => 'Ver Logs'];
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
                'icon' => 'bolt',
                'label' => 'Redis',
                'message' => 'Caché Activa',
                'details' => "Mem: {$mem} · Keys: " . ($info['db0']['keys'] ?? 0)
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'icon' => 'bolt', 'label' => 'Redis', 'message' => 'Servicio Caído', 'details' => 'No alcanzable'];
        }
    }

    private function checkN8n()
    {
        $url = config('ai.n8n_webhook_url');
        if (!$url) return ['status' => 'degraded', 'icon' => 'n8n', 'label' => 'Motor n8n', 'message' => 'Sin Configurar'];

        try {
            $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
            $response = Http::timeout(2)->get($baseUrl . '/healthz');
            
            return $response->successful() 
                ? ['status' => 'online', 'icon' => 'n8n', 'label' => 'Motor n8n', 'message' => 'Listo', 'details' => 'Salud OK']
                : ['status' => 'degraded', 'icon' => 'n8n', 'label' => 'Motor n8n', 'message' => 'Sin Respuesta'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'icon' => 'n8n', 'label' => 'Motor n8n', 'message' => 'No alcanzable'];
        }
    }

    private function checkTelegram()
    {
        $token = config('services.telegram-bot-api.token');
        if (!$token) return ['status' => 'degraded', 'icon' => 'telegram', 'label' => 'Telegram', 'message' => 'Token Ausente'];

        try {
            $response = Http::timeout(5)->get("https://api.telegram.org/bot{$token}/getMe");
            return $response->successful() 
                ? ['status' => 'online', 'icon' => 'telegram', 'label' => 'Telegram', 'message' => 'Conectado', 'details' => '@' . ($response->json()['result']['username'] ?? 'bot')]
                : ['status' => 'degraded', 'icon' => 'telegram', 'label' => 'Telegram', 'message' => 'Error de Auth'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'icon' => 'telegram', 'label' => 'Telegram', 'message' => 'Timeout'];
        }
    }

    private function checkOpenRouterCore()
    {
        $key = config('ai.openrouter_key');
        if (!$key) return ['status' => 'degraded', 'icon' => 'cloud', 'label' => 'OpenRouter Core', 'message' => 'Clave Ausente'];
        
        $online = \Illuminate\Support\Facades\Cache::remember('ping_openrouter_core', 300, function () use ($key) {
            try {
                $response = Http::withToken($key)->timeout(3)->get('https://openrouter.ai/api/v1/auth/key');
                if ($response->status() === 429) return 'rate_limit';
                if ($response->serverError()) return 'server_error';
                return $response->successful() ? 'online' : 'error';
            } catch (\Exception $e) {
                return 'offline';
            }
        });

        if ($online === 'online') {
            return ['status' => 'online', 'icon' => 'openrouter', 'label' => 'OpenRouter Core', 'message' => 'API Activa', 'details' => 'Conexión Centralizada OK'];
        } elseif ($online === 'rate_limit') {
            return ['status' => 'warning', 'icon' => 'openrouter', 'label' => 'OpenRouter Core', 'message' => 'Rate Limit', 'details' => 'Exceso de Peticiones (429)'];
        } elseif ($online === 'server_error') {
            return ['status' => 'danger', 'icon' => 'openrouter', 'label' => 'OpenRouter Core', 'message' => 'Saturado', 'details' => 'Error 502/503 en Hub'];
        }

        return ['status' => 'danger', 'icon' => 'openrouter', 'label' => 'OpenRouter Core', 'message' => 'API Inaccesible', 'details' => 'Verificar Status Page'];
    }

    private function checkAiRouting()
    {
        // En una implementación real más compleja se miraría la base de datos de auditoría
        // para detectar si se ha usado un fallback recientemente.
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('ai_routes')) {
                return ['status' => 'degraded', 'icon' => 'route', 'label' => 'Routing IA', 'message' => 'Pendiente DB', 'details' => 'Falta migración'];
            }
            $routesCount = \App\Models\AiRoute::count();
            return ['status' => 'online', 'icon' => 'route', 'label' => 'Routing IA', 'message' => 'Rutas Activas', 'details' => "{$routesCount} Tareas Mapeadas"];
        } catch (\Exception $e) {
            return ['status' => 'degraded', 'icon' => 'route', 'label' => 'Routing IA', 'message' => 'Error BD', 'details' => 'Fallo al leer rutas'];
        }
    }

    private function getActiveSessionsCount()
    {
        // Al usar JWT, no hay sesiones tradicionales. 
        // El middleware JwtAuth registra la presencia en Redis (user:active:ID) con 15min TTL.
        try {
            // Contamos las llaves en la conexión default de Redis
            $keys = Redis::keys('user:active:*');
            return count($keys);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getNetworkMetrics()
    {
        $rxFile = '/sys/class/net/eth0/statistics/rx_bytes';
        $txFile = '/sys/class/net/eth0/statistics/tx_bytes';

        if (file_exists($rxFile) && file_exists($txFile)) {
            $rx = (int)trim(file_get_contents($rxFile));
            $tx = (int)trim(file_get_contents($txFile));
            
            return [
                'rx' => $this->formatBytes($rx),
                'tx' => $this->formatBytes($tx),
            ];
        }

        return ['rx' => 'N/A', 'tx' => 'N/A'];
    }


    private function getGitMetrics()
    {
        try {
            // Path seguro del repo para git
            $path = base_path();
            $hash = trim(shell_exec("git -C {$path} rev-parse --short HEAD") ?? 'N/A');
            $timestamp = trim(shell_exec("git -C {$path} log -1 --format=%ct") ?? null);
            
            $date = $timestamp ? Carbon::createFromTimestamp($timestamp)->diffForHumans() : 'N/A';
            
            return [
                'hash' => $hash,
                'date' => $date,
            ];
        } catch (\Exception $e) {
            return ['hash' => 'N/A', 'date' => 'N/A'];
        }
    }
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
