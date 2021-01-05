<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class automobile extends Model
{
    protected $table="automobile";
  
    public $timestamps = false;
    protected $fillable = [
      'couleur','kilometre','puissance', 'boite','carburant','jante','cylindre','categorie','capacite','typeoperation','idannonce'
    ];
    public function annonce(){
      return $this->belongsTo('App\annonce','idannonce');
  }
}
