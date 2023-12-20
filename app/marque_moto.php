<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marque_moto extends Model
{
    protected $table="marque_moto";
    public $timestamps = false;
    protected $primaryKey = "idmarquemoto";

    protected $fillable = [
        'designation_marquemoto', 'image_marquemoto' 
    ];
}
