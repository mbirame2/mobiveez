<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categorie extends Model
{
    protected $table="categorie";
    public $timestamps = false;
    protected $primaryKey = "id_cat";

    protected $fillable = [
        'id_cat', 'lib_cat','nom_cat','lib_caten', 'icone'
    ];


 
}
