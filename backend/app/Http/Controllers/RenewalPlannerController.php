<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client;
use App\Models\RenewalLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RenewalPlannerController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $selectedStatuses = $request->get('statuses', []);
        $year = now()->year;

        $query = Contract::with(['client.contacts', 'client.inventoryDaemons'])
            ->whereMonth('end_date', $month);

        if (!empty($selectedStatuses)) {
            $query->whereIn('status', $selectedStatuses);
        }

        $pendingRenewals = $query->get()->groupBy('client_id');

        // Obtener logs de este mes/año para saber quién está completado
        $completedLogs = RenewalLog::where('month', $month)
            ->where('year', $year)
            ->pluck('client_id')
            ->toArray();

        // Lista de estados para el filtro
        $availableStatuses = Contract::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->sort()
            ->values();

        return view('renewal-planner.index', compact('pendingRenewals', 'completedLogs', 'month', 'availableStatuses', 'selectedStatuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'month' => 'required|integer|between:1,12',
            'notes' => 'nullable|string',
        ]);

        RenewalLog::updateOrCreate(
            [
                'client_id' => $request->client_id,
                'month' => $request->month,
                'year' => now()->year,
            ],
            [
                'user_id' => auth()->id(),
                'sent_at' => now(),
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Renovación marcada como enviada.');
    }
}
