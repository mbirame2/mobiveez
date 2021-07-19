<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class commanderestauration extends Model
{
    protected $table="commanderestauration";
    public $timestamps = false;
    protected $primaryKey = "idcommanderestauration";
    protected $fillable = [
        'idmenu', 'adresselivraison', 'prixpizza', 'idmembre', 'quantite','besoin','referencecommande','datelivraison','heurelivraison','datecommande','statut','accompagnements','destinataire','place','feedback','motif'
    ];
}
