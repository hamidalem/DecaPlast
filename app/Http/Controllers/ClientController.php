<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return Inertia::render('Clients/Index', ['clients' => $clients]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_client' => 'required|string|max:50|unique:clients,nom_client',
            'num_tel_client' => 'nullable|string|max:50',
            'adresse_client' => 'nullable|string|max:50',
        ]);

        Client::create($request->all());

        // Redirect back to the bon vente creation page with a success message
        return redirect()->route('bon-ventes.create')->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        return Inertia::render('Clients/Show', ['client' => $client]);
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', ['client' => $client]);
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom_client' => 'required|string|max:50|unique:clients,nom_client,'.$client->nom_client.',nom_client',
            'num_tel_client' => 'nullable|string|max:50',
            'adresse_client' => 'nullable|string|max:50',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
