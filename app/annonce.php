<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class annonce extends Model
{
    protected $table="annonces";
  
    protected $fillable = [
       'user_id','user', 'categorie','photo','automobile_id','habillement_id','immobilier_id', 'sous_categorie','type_publication','prix','paiement','quartier','ville','titre','description','dateannonce'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function immobilier(){
        return $this->belongsTo('App\immobilier');
    }
    public function automobile(){
        return $this->belongsTo('App\automobile');
    }
    public function habillement(){
        return $this->belongsTo('App\habillement');
    }
}
