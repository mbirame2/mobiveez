<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class restauration extends Model
{
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="restauration";
    public $timestamps = false;
    protected $primaryKey = "idrestauration";
    protected $fillable = [
        'idhebergement', 'idmembre','id_dep','typerestauration', 'designation', 'description', 'adresse', 'siteweb', 'telephone', 'capacite', 'ouverture','fermeture','tauxreduction','statut'
    ];

    public function membre(){
        return $this->belongsTo('App\User');
    }
}
