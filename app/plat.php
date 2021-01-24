<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class plat extends Model
{
         /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="menu";
    public $timestamps = false;

    protected $fillable = [
        'photo', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche', 'dureepreparation','plat','description'
    ];

    public function restauration(){
        return $this->belongsTo('App\restauration','idrestauration');
    }
}
