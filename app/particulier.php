<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class particulier extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="Particulier";
    public $timestamps = false;

    protected $fillable = [
        'numero_id', 'genre', 'ville','adresse','photo'
    ];

}
