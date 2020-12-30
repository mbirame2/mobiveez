<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class professionnel extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="Professionnel";

    protected $fillable = [
       'numero_id', 'entreprise', 'ville','adresse','telephone_fixe','photo', 'updated_at', 'created_at',
    ];
   
    public $timestamps = false;

    

}
