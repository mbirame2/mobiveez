<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class souscategorie extends Model
{
    protected $table="souscategorie";
    public $timestamps = false;
    protected $primaryKey = "id_souscat";

    protected $fillable = [
        'lib_souscat','nom_souscat','lib_souscaten','id_cat'
    ];
    public function categorie(){
        return $this->belongsTo('App\categorie','id_cat');
    }
}
