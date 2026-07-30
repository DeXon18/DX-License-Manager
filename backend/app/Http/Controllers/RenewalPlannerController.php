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
        $currentYear = (int) now()->year;
        $year = (int) $request->get('year', $currentYear);
        $month = (int) $request->get('month', now()->month);
        
        // Si el año consultado es futuro respecto al actual, no se filtran estados por defecto
        $selectedStatuses = ($year > $currentYear) ? [] : $request->get('statuses', []);

        $query = Contract::with(['client.contacts', 'client.inventoryDaemons'])
            ->whereYear('end_date', $year)
            ->whereMonth('end_date', $month);

        if (!empty($selectedStatuses)) {
            $query->where(function ($q) use ($selectedStatuses) {
                $hasEmpty = in_array('', $selectedStatuses, true) || in_array(null, $selectedStatuses, true);
                $nonEmptyStatuses = array_filter($selectedStatuses, function($val) {
                    return $val !== '' && $val !== null;
                });
                
                if (!empty($nonEmptyStatuses)) {
                    $q->whereIn('status', $nonEmptyStatuses);
                    if ($hasEmpty) {
                        $q->orWhereNull('status')->orWhere('status', '');
                    }
                } elseif ($hasEmpty) {
                    $q->whereNull('status')->orWhere('status', '');
                }
            });
        }

        $pendingRenewals = $query->get()->groupBy('client_id');

        // Obtener logs del mes y año seleccionados para saber quién está completado
        $completedLogs = RenewalLog::where('month', $month)
            ->where('year', $year)
            ->pluck('client_id')
            ->toArray();

        // Años disponibles en los contratos de la BD
        $availableYears = Contract::selectRaw('YEAR(end_date) as yr')
            ->whereNotNull('end_date')
            ->groupBy('yr')
            ->orderBy('yr', 'asc')
            ->pluck('yr')
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (!in_array($currentYear, $availableYears, true)) {
            $availableYears[] = $currentYear;
            sort($availableYears);
        }

        // Lista de estados para el filtro (incluyendo vacíos/null como "")
        $availableStatuses = Contract::distinct()
            ->pluck('status')
            ->map(function ($status) {
                return $status === null ? '' : trim($status);
            })
            ->unique()
            ->sort()
            ->values();

        return view('renewal-planner.index', compact(
            'pendingRenewals',
            'completedLogs',
            'month',
            'year',
            'currentYear',
            'availableYears',
            'availableStatuses',
            'selectedStatuses'
        ));
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

    public function destroy(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = now()->year;

        RenewalLog::where('client_id', $request->client_id)
            ->where('month', $request->month)
            ->where('year', $year)
            ->delete();

        return redirect()->route('renewal-planner.index', ['month' => $request->month])
            ->with('success', 'Acción revertida correctamente.');
    }
}
