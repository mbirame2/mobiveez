<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class souscategorie extends Model
{
    protected $table="souscategorie";
    public $timestamps = false;

    protected $fillable = [
        'lib_souscat', 'id_souscat','nom_souscat','lib_souscaten'
    ];
}
