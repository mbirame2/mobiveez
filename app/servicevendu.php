<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class servicevendu extends Model
{
            /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="servicevendu";
    public $timestamps = false;
    protected $primaryKey = "idvente";
    protected $fillable = [
        'idannonce', 'idservice','etatvente','datefinservice', 'dateachat' 
    ];

   
}
