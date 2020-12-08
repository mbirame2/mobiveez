<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vehicule extends Model
{
             /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="vehicule";
    public $timestamps = false;

    protected $fillable = [
        'photo1', 'photo2','user_id','photo3', 'photo4','marque','modele','prix','type_vehicule','couleur','type_vitesse','carburant','kilometre','capacite','climatisation','autre_specification','description','statut'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
