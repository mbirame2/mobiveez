<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class immobilier extends Model
{
    protected $table="annonce_immobilier";
    public $timestamps = false;
    protected $fillable = [
      'surface','nombre_piece','date','droit_visite','montant'
    ];
}
