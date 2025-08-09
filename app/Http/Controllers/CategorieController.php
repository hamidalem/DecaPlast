<?php
namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategorieController extends Controller
{
public function index()
{
// Order by primary key instead of created_at
$categories = Categorie::orderBy('id_categ', 'desc')->get();

return Inertia::render('Categories/Index', [
'categories' => $categories,
]);
}

public function create()
{
return Inertia::render('Categories/Create');
}

public function store(Request $request)
{
$request->validate([
'nom_categ' => 'required|string|max:100',
]);

Categorie::create([
'nom_categ' => $request->nom_categ,
]);

return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès');
}

public function edit($id)
{
$categorie = Categorie::findOrFail($id);

return Inertia::render('Categories/Edit', [
'categorie' => $categorie,
]);
}

public function update(Request $request, $id)
{
$request->validate([
'nom_categ' => 'required|string|max:100',
]);

$categorie = Categorie::findOrFail($id);
$categorie->update([
'nom_categ' => $request->nom_categ,
]);

return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès');
}

public function destroy($id)
{
$categorie = Categorie::findOrFail($id);
$categorie->delete();

return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès');
}
}
