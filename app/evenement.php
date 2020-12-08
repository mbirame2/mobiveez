<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class evenement extends Model
{
              /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="evenement_loisir";
    public $timestamps = false;

    protected $fillable = [
        'photo1', 'photo2','user_id','photo3', 'photo4','adresse','frequence','horaire','num_autorisation','information_supplementaire','description','statut','prix'
    ];


    public function user(){
        return $this->belongsTo('App\User');
    }
}
