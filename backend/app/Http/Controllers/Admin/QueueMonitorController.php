<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DockerMonitorService;

class QueueMonitorController extends Controller
{
    /**
     * Display the queue monitor dashboard.
     */
    public function index()
    {
        return view('admin.queue.index');
    }

    /**
     * Fetch logs from the queue container.
     */
    public function logs(DockerMonitorService $dockerMonitor)
    {
        $env = config('app.env') === 'production' ? 'prod' : 'beta';
        $containerName = "dx-queue-{$env}";
        
        // Obtenemos las últimas 200 líneas para el monitor interactivo
        $logs = $dockerMonitor->getContainerLogs($containerName, 200);
        
        return response()->json([
            'logs' => $logs
        ]);
    }
}
