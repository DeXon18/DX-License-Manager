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
        $year = now()->year;

        // Buscamos contratos cuyo end_date sea en el mes seleccionado (cualquier año)
        // Agrupamos por cliente para que sea una lista de tareas por cliente
        $pendingRenewals = Contract::with(['client.contacts', 'client.inventoryDaemons'])
            ->whereMonth('end_date', $month)
            ->get()
            ->groupBy('client_id');

        // Obtener logs de este mes/año para saber quién está completado
        $completedLogs = RenewalLog::where('month', $month)
            ->where('year', $year)
            ->pluck('client_id')
            ->toArray();

        return view('renewal-planner.index', compact('pendingRenewals', 'completedLogs', 'month'));
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

        return back()->with('success', 'Renovación marcada como completada.');
    }
}
