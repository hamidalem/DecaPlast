<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
use HasFactory;

protected $table = 'categories';
protected $primaryKey = 'id_categ';
public $incrementing = true;

public $timestamps = false;
protected $keyType = 'int';

protected $fillable = [
'nom_categ',
];
}
