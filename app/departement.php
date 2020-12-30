<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class departement extends Model
{
    protected $table="departement";
    public $timestamps = false;

    protected $fillable = [
        'lib_dept', 'id_reg','id_dept'
    ];
}
