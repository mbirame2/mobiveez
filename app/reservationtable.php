<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reservationtable extends Model
{
    protected $table="reservationtable";
    public $timestamps = false;
    protected $primaryKey = "idreservationtable";
    protected $fillable = [
        'idmembre', 'idrestauration', 'titre', 'invite','referencereservationtable', 'nombrepersonne', 'datearrivee' , 'heurearrivee' , 'besoins' , 'datereservation' , 'statut'
    ];
}
