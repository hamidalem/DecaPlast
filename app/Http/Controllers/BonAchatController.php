<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BonAchatController extends Controller
{
    public function index()
    {
        $bonAchats = BonAchat::with(['fournisseur', 'produits.depots'])
            ->get()
            ->map(function ($bonAchat) {
                // Calculate if all products are fully assigned to depots
                $fullyAssigned = true;
                foreach ($bonAchat->produits as $produit) {
                    $totalAssigned = $produit->depots->sum('pivot.qte_depot');
                    if ($totalAssigned < $produit->pivot->qte_achat) {
                        $fullyAssigned = false;
                        break;
                    }
                }

                return [
                    'n_ba' => $bonAchat->n_ba,
                    'date_ba' => $bonAchat->date_ba,
                    'etat_ba' => $bonAchat->etat_ba,
                    'nom_fourn' => $bonAchat->nom_fourn,
                    'fournisseur' => $bonAchat->fournisseur,
                    'produits' => $bonAchat->produits,
                    'total_amount' => $bonAchat->produits->sum(function ($product) {
                        return $product->pivot->qte_achat * $product->pivot->prix_achat;
                    }),
                    'montant_verse' => $bonAchat->montant_verse,
                    'reste_a_payer' => $bonAchat->produits->sum(function ($product) {
                            return $product->pivot->qte_achat * $product->pivot->prix_achat;
                        }) - ($bonAchat->montant_verse ?? 0),
                    'statut_depot' => $bonAchat->statut_depot, // Make sure this is included
                    'fully_assigned' => $fullyAssigned,
                    'product_count' => $bonAchat->produits->count(),
                ];
            });

        $fournisseurs = Fournisseur::all();

        return Inertia::render('BonAchats/Index', [
            'bonAchats' => $bonAchats,
            'fournisseurs' => $fournisseurs
        ]);
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $produits = Produit::all();

        return Inertia::render('BonAchats/Create', [
            'fournisseurs' => $fournisseurs,
            'produits' => $produits,
            'defaultDate' => now()->format('Y-m-d')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_ba' => 'required|date',
            'nom_fourn' => 'required|exists:fournisseurs,nom_fourn',
            'products' => 'required|array|min:1',
            'products.*.id_prod' => 'required|exists:produits,id_prod',
            'products.*.qte_achat' => 'required|integer|min:1',
            'products.*.prix_achat' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0',
        ]);

        // Calculate total amount
        $totalAmount = collect($request->products)->sum(function ($product) {
            return $product['qte_achat'] * $product['prix_achat'];
        });

        // Create without etat_ba first
        $bonAchat = BonAchat::create([
            'date_ba' => $request->date_ba,
            'nom_fourn' => $request->nom_fourn,
            'montant_verse' => $request->montant_verse,
            'montant_total' => $totalAmount,
        ]);

        // Attach products
        foreach ($request->products as $product) {
            $bonAchat->produits()->attach($product['id_prod'], [
                'qte_achat' => $product['qte_achat'],
                'prix_achat' => $product['prix_achat']
            ]);
        }

        // Now determine and update etat_ba after products are attached
        $bonAchat->update([
            'etat_ba' => $request->montant_verse >= $totalAmount ? 'paye' : 'verse'
        ]);

        return redirect()->route('bon-achats.index')->with('success', 'Bon d\'achat créé avec succès.');
    }


    public function show(BonAchat $bonAchat)
    {
        $bonAchat->load('fournisseur', 'produits');

        return Inertia::render('BonAchats/Show', [
            'bonAchat' => $bonAchat,
            'totalAmount' => $bonAchat->produits->sum(function ($product) {
                return $product->pivot->qte_achat * $product->pivot->prix_achat;
            }),
            'montantVerse' => $bonAchat->montant_verse ?? 0,
        ]);
    }

    public function edit(BonAchat $bonAchat)
    {
        $bonAchat->load('produits');
        $fournisseurs = Fournisseur::all();
        $produits = Produit::all();

        // Format products for the form
        $formProducts = $bonAchat->produits->map(function ($product) {
            return [
                'id_prod' => $product->id_prod,
                'nom_prod' => $product->nom_prod,
                'qte_achat' => $product->pivot->qte_achat,
                'prix_achat' => $product->pivot->prix_achat,
            ];
        });

        return Inertia::render('BonAchats/Edit', [
            'bonAchat' => [
                'n_ba' => $bonAchat->n_ba,
                'date_ba' => $bonAchat->date_ba->format('Y-m-d'),
                'nom_fourn' => $bonAchat->nom_fourn,
                'montant_verse' => $bonAchat->montant_verse ?? 0,
                'products' => $formProducts,
            ],
            'fournisseurs' => $fournisseurs,
            'produits' => $produits,
        ]);
    }

    public function update(Request $request, BonAchat $bonAchat)
    {
        // Remove 'etat_ba' from validation rules
        $request->validate([
            'date_ba' => 'required|date',
            'nom_fourn' => 'required|exists:fournisseurs,nom_fourn',
            'products' => 'required|array|min:1',
            'products.*.id_prod' => 'required|exists:produits,id_prod',
            'products.*.qte_achat' => 'required|integer|min:1',
            'products.*.prix_achat' => 'required|numeric|min:0',
            'montant_verse' => 'required|numeric|min:0',
        ]);

        // Calculate total amount from the request data
        $totalAmount = collect($request->products)->sum(function ($product) {
            return $product['qte_achat'] * $product['prix_achat'];
        });

        // Determine etat_ba based on the remaining balance
        $resteAPayer = $totalAmount - ($request->montant_verse ?? 0);
        $etat_ba = ($resteAPayer <= 0) ? 'paye' : 'verse';

        // Update main info, including the calculated 'etat_ba'
        $bonAchat->update([
            'date_ba' => $request->date_ba,
            'etat_ba' => $etat_ba, // <-- Calculated and added here
            'nom_fourn' => $request->nom_fourn,
            'montant_verse' => $request->montant_verse,
        ]);

        // Sync products
        $productsData = [];
        foreach ($request->products as $product) {
            $productsData[$product['id_prod']] = [
                'qte_achat' => $product['qte_achat'],
                'prix_achat' => $product['prix_achat']
            ];
        }
        $bonAchat->produits()->sync($productsData);

        return redirect()->route('bon-achats.index')->with('success', 'Bon d\'achat mis à jour avec succès.');
    }

    public function destroy(BonAchat $bonAchat)
    {
        // Detach all products first
        $bonAchat->produits()->detach();

        // Then delete the bon achat
        $bonAchat->delete();

        return redirect()->route('bon-achats.index')->with('success', 'Bon d\'achat supprimé avec succès.');
    }
}
