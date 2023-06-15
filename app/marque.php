<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marque extends Model
{
    protected $table="marque";
    public $timestamps = false;
    protected $primaryKey = "idmarquevoiture";

    protected $fillable = [
        'designation_marquevoiture', 'lib_image_marquevoiturecat' 
    ];
}
