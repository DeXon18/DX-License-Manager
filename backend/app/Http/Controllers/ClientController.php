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
        $clients = Client::withCount('contracts')
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
