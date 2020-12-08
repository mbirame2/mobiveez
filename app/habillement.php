<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class habillement extends Model
{
    protected $table="annonce_habillement";
    public $timestamps = false;
    protected $fillable = [
      'marque','type','couleur','modele','taille'
    ];
}
