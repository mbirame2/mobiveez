<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invitereservationtable extends Model
{
    protected $table="invitereservationtable";
    public $timestamps = false;
    protected $primaryKey = "idinvitereservationtable";
    protected $fillable = [
        'idreservationtable','idmembre','statut' 
    ];
}
