<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepotController extends Controller
{
    public function index()
    {
        $depots = Depot::with(['produits' => function($query) {
            $query->select('produits.id_prod', 'nom_prod', 'desc_prod')
                ->withPivot('qte_depot');
        }])->get();

        return Inertia::render('Depots/Index', ['depots' => $depots]);
    }

    public function create()
    {
        return Inertia::render('Depots/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'adresse_depot' => 'required|string|max:50',
        ]);

        Depot::create($request->all());

        return redirect()->route('depots.index')->with('success', 'Dépôt créé avec succès.');
    }

    public function show(Depot $depot)
    {
        $depot->load(['produits' => function($query) {
            $query->select('produits.id_prod', 'nom_prod', 'desc_prod')
                ->withPivot('qte_depot');
        }]);

        return Inertia::render('Depots/Show', [
            'depot' => $depot,
            'produits' => $depot->produits
        ]);
    }

    public function edit(Depot $depot)
    {
        return Inertia::render('Depots/Edit', ['depot' => $depot]);
    }

    public function update(Request $request, Depot $depot)
    {
        $request->validate([
            'adresse_depot' => 'required|string|max:50',
        ]);

        $depot->update($request->all());

        return redirect()->route('depots.index')->with('success', 'Dépôt mis à jour avec succès.');
    }

    public function destroy(Depot $depot)
    {
        $depot->delete();
        return redirect()->route('depots.index')->with('success', 'Dépôt supprimé avec succès.');
    }
}
