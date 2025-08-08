<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $table = 'depots';
    protected $primaryKey = 'id_depot';
    public $timestamps = false;

    protected $fillable = [
        'nom_depot',
        'adresse_depot'
    ];

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'quantite_depot', 'id_depot', 'id_prod')
            ->withPivot('qte_depot')
            ->using(QuantiteDepot::class); // We'll create this pivot model
    }
}
