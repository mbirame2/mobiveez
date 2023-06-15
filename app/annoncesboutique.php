<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class annoncesboutique extends Model
{
    protected $table="annonceshowroom";
    protected $primaryKey = "idannonceshowroom";
    protected $fillable = [
       'idannonce','idshowroom','visibilite' 
    ];
    public $timestamps = false;
  
}
