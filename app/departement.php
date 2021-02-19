<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class departement extends Model
{
    protected $table="departement";
    public $timestamps = false;
    protected $primaryKey = "id_dept";
    protected $fillable = [
        'lib_dept', 'id_reg','id_dept'
    ];

    public function region(){
        return $this->belongsTo('App\region','id_reg');
    }
}
