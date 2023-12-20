<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
                    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="notification";
    public $timestamps = false;
    protected $primaryKey = "idnotification";
    protected $fillable = [
        'id_sender','id_receiver','data','timestamp','notification','module' 
    ];
}
