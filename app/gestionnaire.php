<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class gestionnaire extends Model
{
    protected $table="gestionnaire";
    public $timestamps = false;
    protected $primaryKey = "id_gestionnaire";

    protected $fillable = [
        'idmembre', 'idshowroom' ,'date','is_connected'
    ];
}
