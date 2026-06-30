<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\LicenseInventoryProduct;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Gestión de filtros de inventario
        if ($request->has('has_inventory')) {
            session(['client_has_inventory' => true]);
            session(['client_inventory_vendor' => $request->vendor_filter ?? 'all']);
        } elseif ($request->has('clear_inventory')) {
            session()->forget('client_has_inventory');
            session()->forget('client_inventory_vendor');
        }

        $hasInventory = session('client_has_inventory', false);
        $vendorFilter = session('client_inventory_vendor', 'all');

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
            ->when($hasInventory, function($query) use ($vendorFilter) {
                $query->whereHas('inventoryDaemons', function($q) use ($vendorFilter) {
                    if ($vendorFilter === 'siemens') {
                        $q->where('daemon', 'not like', '%moldex%');
                    } elseif ($vendorFilter === 'moldex') {
                        $q->where('daemon', 'like', '%moldex%');
                    }
                });
            })
            ->orderBy('name')
            ->paginate(20);

        $globalMetrics = [
            'total_clients' => Client::count(),
            'total_contracts' => Contract::count(),
            'siemens_licenses' => \App\Models\LicenseInventoryDaemon::where('daemon', 'not like', '%moldex%')->count(),
            'moldex_licenses' => \App\Models\LicenseInventoryDaemon::where('daemon', 'like', '%moldex%')->count(),
        ];

        return view('clients.index', compact('clients', 'globalMetrics'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->loadCount([
            'contracts',
            'inventoryDaemons as siemens_daemons_count' => function($query) {
                $query->where('daemon', 'not like', '%moldex%');
            },
            'inventoryDaemons as moldex_daemons_count' => function($query) {
                $query->where('daemon', 'like', '%moldex%');
            }
        ]);

        $client->load(['contracts.vendor', 'contacts', 'certificates', 'auditResults' => function($query) {
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

    /**
     * Display a listing of clients with unified licenses (multiple sold-tos).
     */
    public function unified(Request $request)
    {
        $daemons = \App\Models\LicenseInventoryDaemon::with('client')
            ->whereNotNull('additional_sold_tos')
            ->where('additional_sold_tos', '!=', '[]')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('clients.unified', compact('daemons'));
    }
}
