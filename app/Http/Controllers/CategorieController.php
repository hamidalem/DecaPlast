<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::with('parent')->get();
        $parentCategories = Categorie::whereNull('id_parent')->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories
        ]);
    }

    public function create()
    {
        $parentCategories = Categorie::all();
        return Inertia::render('Categories/Create', ['parentCategories' => $parentCategories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_categ' => 'required|string|max:100',
            'id_parent' => 'nullable|exists:categories,id_categ',
        ]);

        Categorie::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function show(Categorie $categorie)
    {
        $categorie->load('parent', 'enfants');
        return Inertia::render('Categories/Show', ['categorie' => $categorie]);
    }

    public function edit(Categorie $categorie)
    {
        $parentCategories = Categorie::where('id_categ', '!=', $categorie->id_categ)->get();
        return Inertia::render('Categories/Edit', [
            'categorie' => $categorie,
            'parentCategories' => $parentCategories
        ]);
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom_categ' => 'required|string|max:100',
            'id_parent' => 'nullable|exists:categories,id_categ|not_in:'.$categorie->id_categ,
        ]);

        $categorie->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->enfants()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer une catégorie avec des sous-catégories.');
        }

        $categorie->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}
