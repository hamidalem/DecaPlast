<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BonVente extends Model
{
    use HasFactory;

    protected $table = 'bon_ventes';
    protected $primaryKey = 'n_bv';
    public $timestamps = false;

    protected $fillable = [
        'date_bv',
        'etat_bv',
        'nom_client',
        'montant_total',
        'montant_verse'
    ];

    protected $casts = [
        'date_bv' => 'date',
        'montant_total' => 'decimal:2',
        'montant_verse' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'nom_client', 'nom_client');
    }



    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'quantite_ventes', 'n_bv', 'id_prod')
            ->withPivot(['id_depot', 'qte_vente', 'prix_vente']);
    }

    protected static function booted()
    {
        static::saved(function ($bonVente) {
            // Only update the total amount calculation
            $total = $bonVente->produits()->sum(
                DB::raw('quantite_ventes.qte_vente * quantite_ventes.prix_vente')
            );

            if ($bonVente->montant_total != $total) {
                $bonVente->update(['montant_total' => $total]);
            }
        });
    }

    /**
     * Determine and update the payment status based on amounts
     */
    public function updatePaymentStatus()
    {
        $this->etat_bv = $this->montant_verse >= $this->montant_total ? 'paye' : 'verse';
        $this->save();
    }
}
