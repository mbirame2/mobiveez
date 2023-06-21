<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class zone extends Model
{
                 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="zone";
    protected $primaryKey = "id";
    public $timestamps = false;

    protected $fillable = [
        'departement','arrondissement','commune', 'id_dept'
    ];

    public function departement(){
        return $this->belongsTo('App\departement','id_dept');
    }
}
