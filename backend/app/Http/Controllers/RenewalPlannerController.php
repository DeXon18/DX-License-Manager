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
            'license_file' => 'nullable|file|max:2048', // Max 2MB
        ]);

        $filePath = null;
        if ($request->hasFile('license_file')) {
            $client = Client::findOrFail($request->client_id);
            $fileName = "Renewal_" . now()->format('Ymd') . "_" . $request->license_file->getClientOriginalName();
            // Guardamos en la carpeta del cliente
            $filePath = $request->file('license_file')->storeAs(
                'renewals/' . $client->id,
                $fileName
            );
        }

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
                'file_path' => $filePath,
            ]
        );

        return back()->with('success', 'Renovación procesada y registrada correctamente.');
    }

    public function download(RenewalLog $log)
    {
        if (!$log->file_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($log->file_path)) {
            return back()->with('error', 'El archivo de licencia no existe o ha sido movido.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($log->file_path);
    }
}
