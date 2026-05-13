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
            'license_files.*' => 'nullable|file|max:4096', // Max 4MB per file
        ]);

        DB::transaction(function () use ($request) {
            $log = RenewalLog::updateOrCreate(
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

            if ($request->hasFile('license_files')) {
                $client = Client::findOrFail($request->client_id);
                foreach ($request->file('license_files') as $file) {
                    $fileName = "Renewal_" . now()->format('Ymd') . "_" . $file->getClientOriginalName();
                    $path = $file->storeAs('renewals/' . $client->id, $fileName);

                    $log->files()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Renovación procesada con todos los archivos adjuntos.');
    }

    public function downloadFile(\App\Models\RenewalLogFile $file)
    {
        if (!$file->file_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($file->file_path)) {
            return back()->with('error', 'El archivo de licencia no existe o ha sido movido.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($file->file_path, $file->file_name);
    }
}
