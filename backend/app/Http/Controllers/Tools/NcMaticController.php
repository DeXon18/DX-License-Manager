<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\NcmaticLicense;
use Illuminate\Http\Request;

class NcMaticController extends Controller
{
    public function index(Request $request)
    {
        $selectedClientId = $request->get('client_id');

        // Obtener únicamente clientes que tengan contratos de NCmatic
        $clients = Client::whereHas('contracts', function ($q) {
            $q->where('type_product', 'like', '%NCmatic%')
              ->orWhere('sub_product', 'like', '%NCmatic%')
              ->orWhere('comment', 'like', '%NCmatic%');
        })->orderBy('name', 'asc')->get();

        return view('tools.ncmatic', compact('clients', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:ncmatic_licenses,id',
            'client_id' => 'required|exists:clients,id',
            'serial_number' => 'required|string|max:255',
            'license_type' => 'required|string|max:100',
            'expiration_date' => 'nullable|date',
            'status' => 'required|string|in:active,dropped,expired',
            'notes' => 'nullable|string',
        ]);

        $validated['seats'] = 1;

        NcmaticLicense::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return back()->with('success', 'Licencia NCmatic guardada correctamente.');
    }

    public function destroy(NcmaticLicense $license)
    {
        $license->delete();
        return back()->with('success', 'Licencia NCmatic eliminada correctamente.');
    }
}
