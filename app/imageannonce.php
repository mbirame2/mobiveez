<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imageannonce extends Model
{
    //
    protected $table="imageannonce";
    public $timestamps = false;
    protected $fillable = [
      'idannone','urlimage','parametre' 
    ];
}
