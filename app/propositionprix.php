<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class propositionprix extends Model
{
    protected $table="propositionprix";
    public $timestamps = false;
    protected $primaryKey = "idproposition";
    protected $fillable = [
      'idmembre','idannonce','prixproposition','urlimageoffre','description','dateproposition'
    ];
}
