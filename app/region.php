<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    protected $table="region";
    public $timestamps = false;
    protected $primaryKey = "id_reg";
    protected $fillable = [
        'lib_reg', 'id_pays'
    ];

    public function pays(){
        return $this->belongsTo('App\pays2','id_pays');
    }
}
