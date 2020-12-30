<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    protected $table="region";
    public $timestamps = false;

    protected $fillable = [
        'lib_reg', 'id_pays'
    ];
}
