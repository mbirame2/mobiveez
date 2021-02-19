<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class service extends Model
{
               /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="services";
    public $timestamps = false;
    protected $primaryKey = "idService";
    protected $fillable = [
        'nomService', 'module','montantService','nbjour', 'nomcomplet' 
    ];
}
