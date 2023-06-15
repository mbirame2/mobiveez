<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categorierestaurant extends Model
{
    protected $table="categorierestaurant";
    protected $primaryKey = "id_cat";

    protected $fillable = [
        'id_cat', 'lib_cat','nom_cat','lib_caten', 'icone'
    ];
}
