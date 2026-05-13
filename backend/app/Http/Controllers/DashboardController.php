<?php

namespace App\Http\Controllers;

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

        // 1. Calcular métricas (solo productos activos)
        $activeLicenses = LicenseInventoryProduct::active();

        $metrics = [
            'total' => (clone $activeLicenses)->count(),
            
            'critical' => (clone $activeLicenses)
                ->where(function ($query) use ($now) {
                    $query->whereDate('expiration_date', '<=', $now->copy()->addDays(7));
                })->count(),

            'upcoming' => (clone $activeLicenses)
                ->whereDate('expiration_date', '>', $now->copy()->addDays(7))
                ->whereDate('expiration_date', '<=', $now->copy()->addDays(30))
                ->count(),

            'monitoring' => (clone $activeLicenses)
                ->whereDate('expiration_date', '>', $now->copy()->addDays(30))
                ->whereDate('expiration_date', '<=', $now->copy()->addDays(90))
                ->count(),
        ];

        // 2. Top 10 vencimientos inminentes (agrupados por Daemon/Sold-To)
        $upcomingExpirations = LicenseInventoryProduct::with(['daemon.client'])
            ->active()
            ->whereNotNull('expiration_date')
            ->selectRaw('daemon_id, MIN(expiration_date) as expiration_date')
            ->groupBy('daemon_id')
            ->orderBy('expiration_date', 'asc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('metrics', 'upcomingExpirations'));
    }
}
