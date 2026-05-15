<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'receives_alerts' => 'boolean',
        ]);

        $validated['receives_alerts'] = $request->has('receives_alerts');

        $client->contacts()->create($validated);

        return redirect()->route('clients.show', [$client, 'tab' => 'contacts'])
            ->with('success', 'Contacto creado correctamente.');
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, Client $client, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'receives_alerts' => 'boolean',
        ]);

        $validated['receives_alerts'] = $request->has('receives_alerts');

        $contact->update($validated);

        return redirect()->route('clients.show', [$client, 'tab' => 'contacts'])
            ->with('success', 'Contacto actualizado correctamente.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Client $client, Contact $contact)
    {
        $contact->delete();

        return redirect()->route('clients.show', [$client, 'tab' => 'contacts'])
            ->with('success', 'Contacto eliminado correctamente.');
    }
}
