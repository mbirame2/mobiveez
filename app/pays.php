<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pays extends Model
{
    protected $table="pays2";
    public $timestamps = false;
    protected $primaryKey = "id_pays";
    protected $fillable = [
        'lib_pays', 'id_pays'
    ];
}
