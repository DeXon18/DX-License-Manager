<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Persistencia del filtro de inventario en sesión
        if ($request->has('has_inventory')) {
            session(['client_has_inventory' => true]);
        } elseif ($request->has('clear_inventory')) {
            session()->forget('client_has_inventory');
        }

        $hasInventory = session('client_has_inventory', false);

        $clients = Client::withCount([
            'contracts',
            'inventoryDaemons',
            'inventoryDaemons as siemens_daemons_count' => function($query) {
                $query->where('daemon', 'not like', '%moldex%');
            },
            'inventoryDaemons as moldex_daemons_count' => function($query) {
                $query->where('daemon', 'like', '%moldex%');
            }
        ])
            ->when($request->search, function($query) use ($request) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('contracts', function($cq) use ($search) {
                          $cq->where('contract_number', 'like', '%' . $search . '%')
                            ->orWhere('status', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($hasInventory, function($query) {
                $query->has('inventoryDaemons');
            })
            ->orderBy('name')
            ->paginate(20);


        return view('clients.index', compact('clients'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['contracts', 'contacts', 'certificates', 'auditResults' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'codCertificates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Cargar inventario agrupado por Sold-To
        $inventoryBySoldTo = \App\Models\LicenseInventoryDaemon::with(['products' => function($q) {
            $q->orderBy('status')->orderBy('product_code');
        }])
        ->where('client_id', $client->id)
        ->get()
        ->groupBy('sold_to');
        
        return view('clients.show', compact('client', 'inventoryBySoldTo'));
    }
}
