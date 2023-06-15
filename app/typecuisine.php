<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class typecuisine extends Model
{
                   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="typecuisine";
    public $timestamps = false;
    protected $primaryKey = "idtypecuisine";
    protected $fillable = [
        'typecuisine', 'typecuisineen' 
    ];
}
