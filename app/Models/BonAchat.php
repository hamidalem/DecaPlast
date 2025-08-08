<?php


namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class BonAchat extends Model
{
    use HasFactory;

    // Payment statuses
    const ETAT_PAYE = 'payé';
    const ETAT_VERSE = 'versé';

    // Depot assignment statuses
    const STATUT_DEPOT_NON_ASSIGNE = 'non_assigné';
    const STATUT_DEPOT_PARTIEL = 'partiellement_assigné';
    const STATUT_DEPOT_COMPLET = 'assigné';

    protected $table = 'bon_achats';
    protected $primaryKey = 'n_ba';
    public $timestamps = false;

    protected $fillable = [
        'date_ba',
        'etat_ba',       // Payment status
        'statut_depot',  // Depot assignment status
        'nom_fourn',
        'montant_total',
        'montant_verse'
    ];

    protected $casts = [
        'date_ba' => 'date',
        'montant_total' => 'decimal:2',
        'montant_verse' => 'decimal:2',
    ];

    public static function getPaymentStatuses()
    {
        return [
            self::ETAT_PAYE => 'Payé',
            self::ETAT_VERSE => 'Versé',
        ];
    }

    public static function getDepotStatuses()
    {
        return [
            self::STATUT_DEPOT_NON_ASSIGNE => 'Non assigné',
            self::STATUT_DEPOT_PARTIEL => 'Partiellement assigné',
            self::STATUT_DEPOT_COMPLET => 'Entièrement assigné',
        ];
    }


    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'nom_fourn', 'nom_fourn');
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'quantite_achats', 'n_ba', 'id_prod')
            ->withPivot('qte_achat', 'prix_achat', 'assigned_quantity');
    }

    protected static function booted()
    {
        static::saved(function ($bonAchat) {
            // Only update the total amount calculation
            $total = $bonAchat->produits()->sum(
                DB::raw('quantite_achats.qte_achat * quantite_achats.prix_achat')
            );

            if ($bonAchat->montant_total != $total) {
                $bonAchat->update(['montant_total' => $total]);
            }
        });
    }


}
