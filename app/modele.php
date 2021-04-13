<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class modele extends Model
{
    protected $table="modele";
    public $timestamps = false;
    protected $primaryKey = "idmodelevoiture";

    protected $fillable = [
        'idmarquevoiture', 'designation_modelevoiture' ,'designation_modelevoitureen','designation_modelevoitureeng' 
    ];
}
