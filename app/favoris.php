<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class favoris extends Model
{
    protected $table="favoris";
    public $timestamps = false;
    protected $primaryKey = "idfavoris";

    protected $fillable = [
        'id_membre', 'id_annonce' ,'id_showroom'
    ];
}
