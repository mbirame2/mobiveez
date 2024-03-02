<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imageannonce extends Model
{
    //
    protected $table="imageannonce";
    protected $primaryKey = "idimageannonce";
    public $timestamps = false;
    protected $fillable = [
      'idannonce','urlimage','parametre','idimageannonce'
    ];
}
