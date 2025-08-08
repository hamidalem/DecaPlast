<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\Depot;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepotAssignmentController extends Controller
{
    public function create($bonAchatId, $produitId = null)
    {
        $bonAchat = BonAchat::with(['produits' => function($query) use ($produitId) {
            $query->withPivot(['qte_achat', 'assigned_quantity']);
            if ($produitId) {
                $query->where('quantite_achats.id_prod', $produitId); // Specify table name
            }
        }])->findOrFail($bonAchatId);

        $depots = Depot::all();

        // If product ID is provided, work with that single product
        if ($produitId) {
            $produit = $bonAchat->produits->first();

            if (!$produit) {
                abort(404, 'Product not found in this purchase order');
            }

            return Inertia::render('DepotAssignments/Create', [
                'bonAchat' => $bonAchat,
                'depots' => $depots,
                'produit' => [
                    'id_prod' => $produit->id_prod,
                    'nom_prod' => $produit->nom_prod,
                    'qte_achat' => $produit->pivot->qte_achat,
                    'assigned_quantity' => $produit->pivot->assigned_quantity,
                    'remaining_quantity' => $produit->pivot->qte_achat - $produit->pivot->assigned_quantity
                ]
            ]);
        }

        // Otherwise, show all products (original behavior)
        $produits = $bonAchat->produits->map(function($produit) {
            return [
                'id_prod' => $produit->id_prod,
                'nom_prod' => $produit->nom_prod,
                'qte_achat' => $produit->pivot->qte_achat,
                'assigned_quantity' => $produit->pivot->assigned_quantity,
                'remaining_quantity' => $produit->pivot->qte_achat - $produit->pivot->assigned_quantity
            ];
        })->filter(fn($p) => $p['remaining_quantity'] > 0);

        return Inertia::render('DepotAssignments/Create', [
            'bonAchat' => $bonAchat,
            'depots' => $depots,
            'produits' => $produits
        ]);
    }

    public function store(Request $request, $bonAchatId)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.id_prod' => 'required|exists:produits,id_prod',
            'assignments.*.id_depot' => 'required|exists:depots,id_depot',
            'assignments.*.qte_depot' => 'required|integer|min:1',
        ]);

        $bonAchat = BonAchat::with(['produits' => function($query) {
            $query->withPivot(['qte_achat', 'assigned_quantity']);
        }])->findOrFail($bonAchatId);

        DB::transaction(function () use ($request, $bonAchat) {
            // Group assignments by product to calculate total quantities
            $assignmentsByProduct = collect($request->assignments)
                ->groupBy('id_prod')
                ->map(function ($assignments, $productId) use ($bonAchat) {
                    $produit = $bonAchat->produits->firstWhere('id_prod', $productId);
                    $totalAssigned = $assignments->sum('qte_depot');
                    $remaining = $produit->pivot->qte_achat - $produit->pivot->assigned_quantity;

                    if ($totalAssigned > $remaining) {
                        throw new \Exception("La quantité totale demandée dépasse la quantité disponible pour le produit ID: $productId");
                    }

                    return [
                        'produit' => $produit,
                        'assignments' => $assignments,
                        'totalAssigned' => $totalAssigned
                    ];
                });

            // First process all depot assignments
            foreach ($assignmentsByProduct as $productData) {
                foreach ($productData['assignments'] as $assignment) {
                    $depot = Depot::find($assignment['id_depot']);
                    $existingPivot = $depot->produits()->where('produits.id_prod', $assignment['id_prod'])->first();

                    if ($existingPivot) {
                        $depot->produits()->updateExistingPivot($assignment['id_prod'], [
                                'qte_depot' => $existingPivot->pivot->qte_depot + $assignment['qte_depot']]
                    );
                    } else {
                        $depot->produits()->attach($assignment['id_prod'], [
                                'qte_depot' => $assignment['qte_depot']]
                    );
                    }
                }
            }

            // Then update the bonAchat assigned quantities
            foreach ($assignmentsByProduct as $productId => $productData) {
                $bonAchat->produits()->updateExistingPivot($productId, [
                    'assigned_quantity' => $productData['produit']->pivot->assigned_quantity + $productData['totalAssigned']
                ]);
            }

            // Update product-specific status
            $this->updateProductAssignmentStatus($bonAchat);
        });

        return redirect()
            ->route('bon-achats.show', $bonAchatId)
            ->with('success', 'Produits assignés au dépôt avec succès');
    }

    private function updateProductAssignmentStatus(BonAchat $bonAchat)
    {
        $bonAchat->refresh();

        // Update each product's assignment status
        foreach ($bonAchat->produits as $produit) {
            $status = $produit->pivot->assigned_quantity == 0
                ? 'non_assigné'
                : ($produit->pivot->assigned_quantity >= $produit->pivot->qte_achat
                    ? 'entièrement_assigné'
                    : 'partiellement_assigné');

            // You might want to store this in a pivot table column if needed
            // For now, we'll just update the bonAchat's overall status
        }

        // Update overall bonAchat status
        $totalAssigned = $bonAchat->produits->sum('pivot.assigned_quantity');
        $totalToAssign = $bonAchat->produits->sum('pivot.qte_achat');

        $statutDepot = $totalAssigned == 0
            ? BonAchat::STATUT_DEPOT_NON_ASSIGNE
            : ($totalAssigned >= $totalToAssign
                ? BonAchat::STATUT_DEPOT_COMPLET
                : BonAchat::STATUT_DEPOT_PARTIEL);

        $bonAchat->update(['statut_depot' => $statutDepot]);
    }


    public function edit($bonAchatId, $produitId)
    {
        $bonAchat = BonAchat::with(['produits' => function($query) use ($produitId) {
            $query->where('quantite_achats.id_prod', $produitId)
                ->withPivot(['qte_achat', 'assigned_quantity']);
        }])->findOrFail($bonAchatId);

        $depots = Depot::all();
        $produit = $bonAchat->produits->first();

        if (!$produit) {
            abort(404, 'Product not found in this purchase order');
        }

        // Get the existing assignments for this product from the quantite_depot table
        $initialAssignments = DB::table('quantite_depot')
            ->where('id_prod', $produit->id_prod)
            ->whereIn('id_depot', $depots->pluck('id_depot'))
            ->select('id_depot', 'qte_depot')
            ->get()
            ->map(function ($assignment) use ($produit) {
                return [
                    'id_prod' => $produit->id_prod,
                    'nom_prod' => $produit->nom_prod,
                    'id_depot' => $assignment->id_depot,
                    'qte_depot' => $assignment->qte_depot,
                    'original_qte' => $assignment->qte_depot, // Track original quantity for comparison
                ];
            })->all();

        return Inertia::render('DepotAssignments/Edit', [
            'bonAchat' => $bonAchat,
            'depots' => $depots,
            'produit' => [
                'id_prod' => $produit->id_prod,
                'nom_prod' => $produit->nom_prod,
                'qte_achat' => $produit->pivot->qte_achat,
                'assigned_quantity' => $produit->pivot->assigned_quantity,
                'initialAssignments' => $initialAssignments,
            ]
        ]);
    }


    public function update(Request $request, $bonAchatId, $produitId)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.id_prod' => 'required|exists:produits,id_prod',
            'assignments.*.id_depot' => 'required|exists:depots,id_depot',
            'assignments.*.qte_depot' => 'required|integer|min:0', // Allow 0 to remove an assignment
        ]);

        $bonAchat = BonAchat::with(['produits' => function($query) use ($produitId) {
            $query->where('quantite_achats.id_prod', $produitId)
                ->withPivot(['qte_achat', 'assigned_quantity']);
        }])->findOrFail($bonAchatId);

        $produit = $bonAchat->produits->first();

        DB::transaction(function () use ($request, $bonAchat, $produit) {
            $totalAssigned = collect($request->assignments)->sum('qte_depot');
            $originalTotal = $produit->pivot->qte_achat;

            if ($totalAssigned > $originalTotal) {
                throw new \Exception("La quantité totale demandée dépasse la quantité d'achat pour le produit ID: {$produit->id_prod}");
            }

            // First, remove all existing assignments for this product from the correct pivot table.
            DB::table('quantite_depot')->where('id_prod', $produit->id_prod)->delete();

            // Then, re-add the new assignments from the request.
            $newAssignments = collect($request->assignments)
                ->filter(fn($a) => $a['qte_depot'] > 0) // Filter out assignments with 0 quantity
                ->map(function ($assignment) {
                    return [
                        'id_prod' => $assignment['id_prod'],
                        'id_depot' => $assignment['id_depot'],
                        'qte_depot' => $assignment['qte_depot'],
                    ];
                })
                ->all();

            // Insert the new assignments into the correct pivot table.
            if (!empty($newAssignments)) {
                DB::table('quantite_depot')->insert($newAssignments);
            }


            // Update the assigned_quantity on the bon_achat pivot table
            $bonAchat->produits()->updateExistingPivot($produit->id_prod, [
                'assigned_quantity' => $totalAssigned
            ]);

            // Update the overall bonAchat status
            $this->updateProductAssignmentStatus($bonAchat);
        });

        return redirect()
            ->route('bon-achats.show', $bonAchat->n_ba)
            ->with('success', 'Assignations de produit mises à jour avec succès');
    }

}
