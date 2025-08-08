<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::with('categorie')->get();
        $categories = Categorie::all();

        return Inertia::render('Produits/Index', [
            'produits' => $produits,
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $categories = Categorie::all();
        return Inertia::render('Produits/Create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_prod' => 'required|string|max:500',
            'desc_prod' => 'nullable|string|max:900',
            'id_categ' => 'nullable|exists:categories,id_categ',
        ]);

        Produit::create($request->all());

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function show(Produit $produit)
    {
        $produit->load('categorie');
        return Inertia::render('Produits/Show', ['produit' => $produit]);
    }

    public function edit(Produit $produit)
    {
        $categories = Categorie::all();
        return Inertia::render('Produits/Edit', [
            'produit' => $produit,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom_prod' => 'required|string|max:500',
            'desc_prod' => 'nullable|string|max:900',
            'id_categ' => 'nullable|exists:categories,id_categ',
        ]);

        $produit->update($request->all());

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
