<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\LicenseInventoryProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the system dashboard.
     */
    public function index(): View
    {
        $now = Carbon::now()->startOfDay();

        // 1. Obtener la fecha mínima de expiración por cada servidor (Daemon/Sold-To) activo
        $daemonExpirations = LicenseInventoryProduct::active()
            ->selectRaw('daemon_id, MIN(expiration_date) as earliest_expiration')
            ->groupBy('daemon_id')
            ->get();

        // 2. Calcular métricas basadas en servidores únicos
        $metrics = [
            'total' => $daemonExpirations->count(),
            
            'critical' => $daemonExpirations->filter(function ($item) use ($now) {
                return $item->earliest_expiration && Carbon::parse($item->earliest_expiration)->lte($now->copy()->addDays(7));
            })->count(),

            'upcoming' => $daemonExpirations->filter(function ($item) use ($now) {
                $date = $item->earliest_expiration ? Carbon::parse($item->earliest_expiration) : null;
                return $date && $date->gt($now->copy()->addDays(7)) && $date->lte($now->copy()->addDays(30));
            })->count(),

            'monitoring' => $daemonExpirations->filter(function ($item) use ($now) {
                $date = $item->earliest_expiration ? Carbon::parse($item->earliest_expiration) : null;
                return $date && $date->gt($now->copy()->addDays(30)) && $date->lte($now->copy()->addDays(90));
            })->count(),
        ];

        // 3. Top vencimientos relevantes (0-90 días, agrupados por Daemon/Sold-To)
        $upcomingExpirations = LicenseInventoryProduct::with(['daemon.client'])
            ->active()
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', $now->copy()->addDays(90))
            ->selectRaw('daemon_id, MIN(expiration_date) as expiration_date')
            ->groupBy('daemon_id')
            ->orderBy('expiration_date', 'asc')
            ->limit(15)
            ->get();

        // 5. Renovaciones del mes actual (para el contador de cara al usuario)
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $renewalsThisMonth = LicenseInventoryProduct::active()
            ->whereBetween('expiration_date', [$monthStart, $monthEnd])
            ->distinct('daemon_id')
            ->count();

        return view('dashboard', compact('metrics', 'upcomingExpirations', 'contractStatuses', 'contractCounts', 'renewalsThisMonth'));
    }
}
