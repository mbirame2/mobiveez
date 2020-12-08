<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class commande_plat extends Model
{
              /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="commande_plat";
    public $timestamps = false;

    protected $fillable = [
        'destinataire_id', 'plat_id','id_user','nombre_plat', 'type','adresse_livraison','date','accompagnement','besoin_particulier','disponible'
    ];


    public function destinataire(){
        return $this->belongsTo('App\User');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function plat(){
        return $this->belongsTo('App\plat');
    }
}
