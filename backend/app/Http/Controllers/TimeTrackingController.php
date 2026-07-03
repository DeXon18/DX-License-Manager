<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class TimeTrackingController extends Controller
{
    public function index()
    {
        return view('tools.time-tracking');
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $clientsQuery = Client::with(['contracts' => function ($q) {
            $q->select('id', 'client_id', 'cost_center', 'type_product', 'sub_product', 'end_date', 'status');
            $q->where('status', '!=', 'Baja')->orWhereNull('status');
        }])->whereHas('contracts', function ($q) {
            $q->where('status', '!=', 'Baja')->orWhereNull('status');
        });

        if (!empty($query)) {
            $clientsQuery->where('name', 'like', '%' . $query . '%');
        }

        $clients = $clientsQuery->get();

        $results = [];

        foreach ($clients as $client) {
            foreach ($client->contracts as $contract) {
                $imputationCode = 'SOPORTE';
                $imputationName = 'SOPORTE';
                
                $cc = $contract->cost_center ?? '';
                
                if (str_contains($cc, '036-A-PP')) {
                    $imputationCode = '100052480';
                    $imputationName = '036-A-PP';
                } elseif (str_contains($cc, '036-A-PD')) {
                    $imputationCode = '100052479';
                    $imputationName = '036-A-PD';
                } elseif (str_contains($cc, '036-A-CA')) {
                    $imputationCode = '100052478';
                    $imputationName = '036-A-CA';
                } elseif (str_contains($cc, '036-A-SU')) {
                    $imputationCode = 'SOPORTE';
                    $imputationName = 'SOPORTE';
                }

                $results[] = [
                    'client_name' => $client->name,
                    'cost_center' => $cc,
                    'type_product' => $contract->type_product,
                    'sub_product' => $contract->sub_product,
                    'end_date' => $contract->end_date ? $contract->end_date->format('d/m/Y') : 'PERMANENTE',
                    'imputation_code' => $imputationCode,
                    'imputation_name' => $imputationName,
                ];
            }
        }

        // Si un cliente tiene múltiples contratos idénticos (ej. mismo cost_center y producto), 
        // podríamos tener duplicados. Para la imputación, los duplicados visuales no estorban, 
        // pero podemos dejarlos para que se vea el detalle de fechas si difieren.

        return response()->json($results);
    }
}
