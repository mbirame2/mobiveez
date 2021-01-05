<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class habillement extends Model
{
    protected $table="habillement";
    public $timestamps = false;
    protected $fillable = [
      'marque','type','couleur','modele','taille','idannonce'
    ];

    public function annonce(){
      return $this->belongsTo('App\annonce','idannonce');
  }
}
