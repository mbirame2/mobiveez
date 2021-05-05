<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class automobile extends Model
{
    protected $table="automobile";
  
    public $timestamps = false;
    protected $primaryKey = "idautomobile";

    protected $fillable = [
      'couleur','kilometre','puissance','idmodelevoiture', 'boite','carburant','jante','cylindre','categorie','capacite','typeoperation','idannonce','vehicule_type','place','climatisation'
    ];
    public function annonce(){
      return $this->belongsTo('App\annonce','idannonce');
  }
}
