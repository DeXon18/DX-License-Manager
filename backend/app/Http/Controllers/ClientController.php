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
        $query = Client::withCount(['contracts' => function ($q) {
            $q->where('status', '!=', 'Baja');
        }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $clients = $query->orderBy('name')->paginate(20);

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
