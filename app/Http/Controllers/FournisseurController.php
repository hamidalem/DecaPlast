<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FournisseurController extends Controller
{
    public function index()
    {
        $fournisseurs = Fournisseur::all();
        return Inertia::render('Fournisseurs/Index', ['fournisseurs' => $fournisseurs]);
    }

    public function create()
    {
        return Inertia::render('Fournisseurs/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_fourn' => 'required|string|max:50|unique:fournisseurs,nom_fourn',
            'num_tel_fourn' => 'nullable|string|max:50',
        ]);

        Fournisseur::create($request->all());

        // The redirection has been updated here to return to the bon-achats create page.
        return redirect()->route('bon-achats.create')->with('success', 'Fournisseur created successfully.');
    }

    public function show(Fournisseur $fournisseur)
    {
        return Inertia::render('Fournisseurs/Show', ['fournisseur' => $fournisseur]);
    }

    public function edit(Fournisseur $fournisseur)
    {
        return Inertia::render('Fournisseurs/Edit', ['fournisseur' => $fournisseur]);
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom_fourn' => 'required|string|max:50|unique:fournisseurs,nom_fourn,'.$fournisseur->nom_fourn.',nom_fourn',
            'num_tel_fourn' => 'nullable|string|max:50',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur updated successfully.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur deleted successfully.');
    }
}
