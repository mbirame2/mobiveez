<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class automobile extends Model
{
    protected $table="annonce_automobile";
  
    public $timestamps = false;
    protected $fillable = [
      'marque','modele','couleur','kilometre','puissance', 'boite','carburant','jante','cylindre','categorie','capacite'
    ];
}
