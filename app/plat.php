<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class plat extends Model
{
         /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="menu";
    public $timestamps = false;
    protected $primaryKey = "idmenu";

    
    protected $fillable = [
        'photo', 'prix',  'prixpetit',  'prixmoyen',  'prixgrand','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche', 'dureepreparation','plat','description','statut','accompagnements','categorie_plat','bloquer_commande'
    ];

    public function restauration(){
        return $this->belongsTo('App\restauration','idrestauration');
    }
}
