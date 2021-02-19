<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
                 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="transaction";
    public $timestamps = false;

    protected $fillable = [
        'id_membre','type','description','date'
    ];

}
