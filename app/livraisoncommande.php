<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class livraisoncommande extends Model
{
    protected $table="livraisoncommande";
    public $timestamps = false;
    protected $primaryKey = "idlivraisoncommande";
    protected $fillable = [
        'idcommande','id_destinataire','id_tariflivraison','adresse','besoins','datelivraisoncommande','statut' 
    ];
}
