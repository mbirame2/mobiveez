<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imagehebergement extends Model
{
    protected $table="imagehebergement";
    public $timestamps = false;
    protected $fillable = [
      'idhebergement','urlimagehebergement','parametreimagehebergement' 
    ];
}
