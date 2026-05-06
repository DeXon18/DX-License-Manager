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
        $client->load(['contracts', 'contacts', 'certificates']);
        
        return view('clients.show', compact('client'));
    }
}
