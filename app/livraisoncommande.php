<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class livraisoncommande extends Model
{
    protected $table="livraisoncommande";
    public $timestamps = false;
    protected $primaryKey = "idlivraisoncommande";
    protected $fillable = [
        'idcommande','iddestinataire','idtariflivraison','adresse','besoins','datelivraisoncommande','statut' 
    ];
}
