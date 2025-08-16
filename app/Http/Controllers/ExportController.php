<?php

namespace App\Http\Controllers;

use App\Models\BonAchat;
use App\Models\BonVente;
use App\Models\Categorie;
use App\Models\Produit;
use Barryvdh\DomPDF\Facade\Pdf;


class ExportController extends Controller
{
    public function exportBonAchatToPdf($n_ba)
    {
        $bonAchat = BonAchat::with(['fournisseur', 'produits'])
            ->where('n_ba', $n_ba)
            ->firstOrFail();

        // Calculate totals
        $totalAmount = $bonAchat->produits->sum(function ($product) {
            return $product->pivot->qte_achat * $product->pivot->prix_achat;
        });

        $montantVerse = $bonAchat->montant_verse ?? 0;
        $resteAPayer = $totalAmount - $montantVerse;

        // Prepare data for the view
        $data = [
            'bonAchat' => $bonAchat,
            'totalAmount' => $totalAmount,
            'montantVerse' => $montantVerse,
            'resteAPayer' => $resteAPayer,
            'dateFormatted' => $bonAchat->date_ba->format('d/m/Y'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.bon-achat', $data);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Download the PDF file
        return $pdf->download("bon-achat-{$n_ba}.pdf");

        // Alternatively, you can use stream() to display in browser:
        // return $pdf->stream("bon-achat-{$n_ba}.pdf");
    }


    public function exportBonVenteToPdf($n_bv)
    {
        // Get the bon vente with relationships
        $bonVente = BonVente::with(['client', 'produits'])
            ->where('n_bv', $n_bv)
            ->firstOrFail();

        // Calculate totals
        $totalAmount = $bonVente->produits->sum(function ($product) {
            return $product->pivot->qte_vente * $product->pivot->prix_vente;
        });

        $montantVerse = $bonVente->montant_verse ?? 0;
        $resteAPayer = $totalAmount - $montantVerse;

        // Prepare data for the view
        $data = [
            'bonVente' => $bonVente,
            'totalAmount' => $totalAmount,
            'montantVerse' => $montantVerse,
            'resteAPayer' => $resteAPayer,
            'dateFormatted' => $bonVente->date_bv->format('d/m/Y'),
        ];

        // Generate PDF using the Pdf facade
        $pdf = Pdf::loadView('pdf.bon-vente', $data);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Download the PDF file
        return $pdf->download("bon-vente-{$n_bv}.pdf");
    }


    public function exportProduitsToPdf()
    {
        // Get all products with their categories
        $produits = Produit::with('categorie')->get();

        // Calculate statistics
        $totalProduits = $produits->count();
        $categoriesCount = Categorie::count();

        // Prepare data for the view
        $data = [
            'produits' => $produits,
            'totalProduits' => $totalProduits,
            'categoriesCount' => $categoriesCount,
            'dateFormatted' => now()->format('d/m/Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.liste-produits', $data);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Download the PDF file
        return $pdf->download("liste-produits-".now()->format('Y-m-d').".pdf");
    }


}
