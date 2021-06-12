<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class commandereservationtable extends Model
{
    protected $table="commandereservationtable";
    public $timestamps = false;
    protected $primaryKey = "idcommandereservationtable";
    protected $fillable = [
        'idmenu', 'idreservationtable', 'idmembre', 'quantite','besoin'
    ];
}
