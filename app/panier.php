<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class panier extends Model
{
    protected $table="panier";
    protected $primaryKey = "idpanier";
    public $timestamps = false;
    protected $fillable = [
      'idmembre','idannonce','date','statut','quantite','idmenu' 
    ];

    public function annonce(){
      return $this->belongsTo('App\annonce','idannonce');
  }
}
