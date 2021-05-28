<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class commande extends Model
{
            /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="commande";
    public $timestamps = false;
    protected $primaryKey = "idcommande";
    protected $fillable = [
       'idpanier','datecommande','statut','quantite','reference','adresse','motif','feedback','datereceptioncommande'
    ];
    public function panier(){
        return $this->belongsTo('App\panier','idpanier');
    }
}
