<?php

namespace App\Http\Controllers;

use App\Models\BonVente;
use App\Models\Client;
use App\Models\Depot;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BonVenteController extends Controller
{
    public function index()
    {
        $bonVentes = BonVente::with(['client', 'produits'])->get()
            ->map(function ($bonVente) {
                $totalAmount = $bonVente->produits->sum(function ($product) {
                    return $product->pivot->qte_vente * $product->pivot->prix_vente;
                });

                return [
                    'n_bv' => $bonVente->n_bv,
                    'date_bv' => $bonVente->date_bv,
                    'etat_bv' => $bonVente->etat_bv,
                    'nom_client' => $bonVente->nom_client,
                    'client' => $bonVente->client,
                    'total_amount' => $totalAmount,
                    'montant_verse' => $bonVente->montant_verse,
                    'reste_a_payer' => $totalAmount - ($bonVente->montant_verse ?? 0),
                    'product_count' => $bonVente->produits->count(),
                    'produits' => $bonVente->produits,
                ];
            });

        $clients = Client::all();

        return Inertia::render('BonVentes/Index', [
            'bonVentes' => $bonVentes,
            'clients' => $clients
        ]);
    }

    public function create()
    {
        $clients = Client::all();
        $produits = Produit::with(['depots' => function($query) {
            $query->withPivot('qte_depot');
        }])->get();

        $depots = Depot::all();

        return Inertia::render('BonVentes/Create', [
            'clients' => $clients,
            'produits' => $produits,
            'depots' => $depots,
            'defaultDate' => now()->format('Y-m-d')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_bv' => 'required|date',
            'nom_client' => 'required|exists:clients,nom_client',
            'products' => 'required|array|min:1',
            'products.*.id_prod' => 'required|exists:produits,id_prod',
            'products.*.id_depot' => 'required|exists:depots,id_depot',
            'products.*.qte_vente' => 'required|integer|min:1',
            'products.*.prix_vente' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0',
        ]);

        // Validate stock quantities
        foreach ($request->products as $product) {
            $availableQty = DB::table('quantite_depot')
                ->where('id_prod', $product['id_prod'])
                ->where('id_depot', $product['id_depot'])
                ->value('qte_depot');

            if ($availableQty === null || $product['qte_vente'] > $availableQty) {
                return back()->withErrors([
                    'products' => "La quantité demandée pour le produit dépasse le stock disponible dans le dépôt"
                ]);
            }
        }

        // Calculate total amount
        $totalAmount = collect($request->products)->sum(function ($product) {
            return $product['qte_vente'] * $product['prix_vente'];
        });

        // Create bonVente
        $bonVente = BonVente::create([
            'date_bv' => $request->date_bv,
            'etat_bv' => ($request->montant_verse >= $totalAmount) ? 'paye' : 'verse',
            'nom_client' => $request->nom_client,
            'montant_total' => $totalAmount,
            'montant_verse' => $request->montant_verse,
        ]);

        // Process products and update depot quantities
        DB::transaction(function () use ($request, $bonVente) {
            foreach ($request->products as $product) {
                // Check if this product-depot combination already exists for this bonVente
                $exists = DB::table('quantite_ventes')
                    ->where('n_bv', $bonVente->n_bv)
                    ->where('id_prod', $product['id_prod'])
                    ->where('id_depot', $product['id_depot'])
                    ->exists();

                if ($exists) {
                    // Update existing entry
                    DB::table('quantite_ventes')
                        ->where('n_bv', $bonVente->n_bv)
                        ->where('id_prod', $product['id_prod'])
                        ->where('id_depot', $product['id_depot'])
                        ->update([
                            'qte_vente' => DB::raw('qte_vente + ' . $product['qte_vente']),
                            'prix_vente' => $product['prix_vente']
                        ]);
                } else {
                    // Create new entry
                    DB::table('quantite_ventes')->insert([
                        'n_bv' => $bonVente->n_bv,
                        'id_prod' => $product['id_prod'],
                        'id_depot' => $product['id_depot'],
                        'qte_vente' => $product['qte_vente'],
                        'prix_vente' => $product['prix_vente']
                    ]);
                }

                // Update depot quantity
                DB::table('quantite_depot')
                    ->where('id_prod', $product['id_prod'])
                    ->where('id_depot', $product['id_depot'])
                    ->decrement('qte_depot', $product['qte_vente']);
            }
        });

        return redirect()->route('bon-ventes.index')->with('success', 'Bon de vente créé avec succès.');
    }

    public function show(BonVente $bonVente)
    {
        $bonVente->load('client', 'produits');

        // Calculate total amount dynamically from attached products
        $totalAmount = $bonVente->produits->sum(function ($product) {
            return $product->pivot->qte_vente * $product->pivot->prix_vente;
        });

        return Inertia::render('BonVentes/Show', [
            'bonVente' => $bonVente,
            'totalAmount' => $totalAmount,
            'montantVerse' => $bonVente->montant_verse ?? 0,
        ]);
    }

    public function edit(BonVente $bonVente)
    {
        $bonVente->load('produits');
        $clients = Client::all();
        $produits = Produit::all();

        $formProducts = $bonVente->produits->map(function ($product) {
            return [
                'id_prod' => $product->id_prod,
                'nom_prod' => $product->nom_prod,
                'qte_vente' => $product->pivot->qte_vente,
                'prix_vente' => $product->pivot->prix_vente,
            ];
        });

        return Inertia::render('BonVentes/Edit', [
            'bonVente' => [
                'n_bv' => $bonVente->n_bv,
                'date_bv' => $bonVente->date_bv->format('Y-m-d'),
                'nom_client' => $bonVente->nom_client,
                'montant_verse' => $bonVente->montant_verse,
                'products' => $formProducts,
            ],
            'clients' => $clients,
            'produits' => $produits,
        ]);
    }

    public function update(Request $request, BonVente $bonVente)
    {
        $request->validate([
            'date_bv' => 'required|date',
            'nom_client' => 'required|exists:clients,nom_client',
            'products' => 'required|array|min:1',
            'products.*.id_prod' => 'required|exists:produits,id_prod',
            'products.*.qte_vente' => 'required|integer|min:1',
            'products.*.prix_vente' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0',
        ]);

        // Calculate total amount from the request data
        $totalAmount = collect($request->products)->sum(function ($product) {
            return $product['qte_vente'] * $product['prix_vente'];
        });

        // Determine etat_bv based on the remaining balance
        $resteAPayer = $totalAmount - ($request->montant_verse ?? 0);
        $etat_bv = ($resteAPayer <= 0) ? 'paye' : 'verse';

        // Update main info, including the calculated 'etat_bv'
        $bonVente->update([
            'date_bv' => $request->date_bv,
            'etat_bv' => $etat_bv,
            'nom_client' => $request->nom_client,
            'montant_total' => $totalAmount,
            'montant_verse' => $request->montant_verse,
        ]);

        // Sync products
        $productsData = [];
        foreach ($request->products as $product) {
            $productsData[$product['id_prod']] = [
                'qte_vente' => $product['qte_vente'],
                'prix_vente' => $product['prix_vente']
            ];
        }
        $bonVente->produits()->sync($productsData);

        return redirect()->route('bon-ventes.index')->with('success', 'Bon de vente mis à jour avec succès.');
    }

    public function destroy(BonVente $bonVente)
    {
        $bonVente->produits()->detach();
        $bonVente->delete();

        return redirect()->route('bon-ventes.index')->with('success', 'Bon de vente supprimé avec succès.');
    }
}
