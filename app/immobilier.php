<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class immobilier extends Model
{
    protected $table="immobilier";
    public $timestamps = false;
    protected $fillable = [
      'surface','nombrepiece','datedisponibilite','droitvisite','montantdroit'
    ];
}
