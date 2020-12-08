<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chambre extends Model
{
          /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="chambre";
    public $timestamps = false;

    protected $fillable = [
        'photo1', 'photo2','user_id','photo3', 'photo4','titre','prix_nuitee','service_chambre','type_lit','capacite','description','statut'
    ];


    public function user(){
        return $this->belongsTo('App\User');
    }
}
