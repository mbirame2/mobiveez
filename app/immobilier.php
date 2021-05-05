<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class immobilier extends Model
{
    protected $table="immobilier";
    protected $primaryKey = "idimmobilier";

    public $timestamps = false;
    protected $fillable = [
      'surface','nombrepiece','datedisponibilite','droitvisite','montantdroit','idannonce'
    ];
}
