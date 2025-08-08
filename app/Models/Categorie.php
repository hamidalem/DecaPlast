<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'id_categ';
    public $timestamps = false;

    protected $fillable = [
        'nom_categ',
        'id_parent',
    ];

    // Parent category relationship
    public function parent()
    {
        return $this->belongsTo(Categorie::class, 'id_parent', 'id_categ');
    }

    // Child categories relationship
    public function children()
    {
        return $this->hasMany(Categorie::class, 'id_parent', 'id_categ');
    }
}
