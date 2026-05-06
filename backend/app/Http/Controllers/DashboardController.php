<?php

namespace App\Http\Controllers;

use App\Models\Contract;
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

        // 1. Calcular métricas (solo contratos que no son BAJA)
        $activeContracts = Contract::where('status', '!=', 'Baja');

        $metrics = [
            'total' => (clone $activeContracts)->count(),
            
            'critical' => (clone $activeContracts)
                ->where(function ($query) use ($now) {
                    $query->whereDate('end_date', '<=', $now->copy()->addDays(7));
                })->count(),

            'upcoming' => (clone $activeContracts)
                ->whereDate('end_date', '>', $now->copy()->addDays(7))
                ->whereDate('end_date', '<=', $now->copy()->addDays(30))
                ->count(),

            'monitoring' => (clone $activeContracts)
                ->whereDate('end_date', '>', $now->copy()->addDays(30))
                ->whereDate('end_date', '<=', $now->copy()->addDays(90))
                ->count(),
        ];

        // 2. Top 10 vencimientos inminentes
        $upcomingExpirations = Contract::with(['client', 'vendor'])
            ->where('status', '!=', 'Baja')
            ->whereNotNull('end_date')
            ->orderBy('end_date', 'asc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('metrics', 'upcomingExpirations'));
    }
}
