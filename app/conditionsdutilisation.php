<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class conditionsdutilisation extends Model
{
    protected $table="conditionsdutilisation";
    public $timestamps = false;
    protected $primaryKey = "id";

    protected $fillable = [
        'type', 'fr','acro','en'
    ];


 
}
