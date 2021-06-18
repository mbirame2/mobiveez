<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class specialite extends Model
{
                    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="specialite";
    public $timestamps = false;

    protected $fillable = [
        'idspecialite','idrestauration','idtypecuisine' 
    ];

}
