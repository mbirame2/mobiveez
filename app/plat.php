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
    protected $table="plat";
    public $timestamps = false;

    protected $fillable = [
        'photo', 'prix','user_id','user', 'temps_preparation','jour_disponible','plat_accompagnement','description','statut','categorie','nombre_plat'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
