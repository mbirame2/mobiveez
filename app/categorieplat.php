<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categorieplat extends Model
{
    protected $table="categorieplat";
    protected $primaryKey = "id_cat";

    protected $fillable = [
        'id_cat', 'lib_cat','nom_cat','lib_caten', 'icone'
    ];
}
