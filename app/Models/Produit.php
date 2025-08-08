<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';
    protected $primaryKey = 'id_prod';
    public $timestamps = false;

    protected $fillable = [
        'id_categ',
        'nom_prod',
        'desc_prod',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categ', 'id_categ');
    }

    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'quantite_depot', 'id_prod', 'id_depot')
            ->withPivot('qte_depot');
    }





    public function bonAchats()
    {
        return $this->belongsToMany(BonAchat::class, 'quantite_achats', 'id_prod', 'n_ba')
            ->withPivot('qte_achat', 'prix_achat');
    }
}
