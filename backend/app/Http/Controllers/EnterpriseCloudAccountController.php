<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EnterpriseCloudAccount;
use Illuminate\Http\Request;

class EnterpriseCloudAccountController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'sold_to' => 'required|string|max:255',
            'account_id' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255',
        ]);

        $client->enterpriseCloudAccounts()->create([
            'sold_to' => $request->sold_to,
            'account_id' => $request->account_id,
            'admin_email' => $request->admin_email,
        ]);

        return redirect()->back()->with('success', 'Cuenta Enterprise Cloud añadida correctamente.');
    }

    public function update(Request $request, Client $client, EnterpriseCloudAccount $enterpriseCloudAccount)
    {
        // Verificar que la cuenta pertenece al cliente
        if ($enterpriseCloudAccount->client_id !== $client->id) {
            abort(403);
        }

        $request->validate([
            'sold_to' => 'required|string|max:255',
            'account_id' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255',
        ]);

        $enterpriseCloudAccount->update([
            'sold_to' => $request->sold_to,
            'account_id' => $request->account_id,
            'admin_email' => $request->admin_email,
        ]);

        return redirect()->back()->with('success', 'Cuenta Enterprise Cloud actualizada.');
    }

    public function destroy(Client $client, EnterpriseCloudAccount $enterpriseCloudAccount)
    {
        if ($enterpriseCloudAccount->client_id !== $client->id) {
            abort(403);
        }

        $enterpriseCloudAccount->delete();

        return redirect()->back()->with('success', 'Cuenta Enterprise Cloud eliminada.');
    }
}
