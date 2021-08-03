<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hebergement extends Model
{
    //
             /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="hebergement";
    protected $primaryKey = "idhebergement";
    public $timestamps = false;

    protected $fillable = [
       'id_dep','idmembre','typehebergement','designation','description','adresse','siteweb','telephone','heurearrivee','heuredepart','nombreetoile','tauxreduction','wifigratuit','restaurationinterne','parking','navetteaeroport','annulationgratuite','installationpourenfant','animaldomestiqueaccepte','statut' ,'piscine'
    ];
}
