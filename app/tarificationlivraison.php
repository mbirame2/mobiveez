<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tarificationlivraison extends Model
{
    protected $table="tarificationlivraison";
  
    public $timestamps = false;
    protected $primaryKey = "id";

    protected $fillable = [
      'id_membre','id_zone','tarif'
    ];

    // public function membre(){
    //   return $this->belongsTo('App\User','id_membre');
    // }

    public function zone(){
        return $this->belongsTo('App\zone','id_zone');
    }
}
