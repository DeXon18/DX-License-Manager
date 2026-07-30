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
        $search = $request->get('search');
        $selectedClientId = $request->get('client_id');

        $query = NcmaticLicense::with('client')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($selectedClientId) {
            $query->where('client_id', $selectedClientId);
        }

        $licenses = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name', 'asc')->get();

        return view('tools.ncmatic.index', compact('licenses', 'clients', 'search', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:ncmatic_licenses,id',
            'client_id' => 'required|exists:clients,id',
            'serial_number' => 'required|string|max:255',
            'license_type' => 'required|string|max:100',
            'seats' => 'required|integer|min:1',
            'expiration_date' => 'nullable|date',
            'status' => 'required|string|in:active,dropped,expired',
            'notes' => 'nullable|string',
        ]);

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
