<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class livraison extends Model
{
    protected $table="livraison";
    protected $primaryKey = "id";

    protected $fillable = [
        'id_dept', 'idmembre','nomExpediteur','poids', 'taille', 'typeColis', 'pointCollecte', 'telephoneDestinataire', 'adresseDestinataire', 'reference', 'nomDestinataire', 'photoColis'
    ];
}
