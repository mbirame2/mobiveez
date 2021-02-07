<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chambre extends Model
{
          /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="chambre";
    public $timestamps = false;

    protected $fillable = [
       'prix','typechambre','capacite','description','typelit','climatisation','douche','baignoire','televiseur','refrigerateur','minibar','eauminerale','balcon','selectionfilm','conditionannulation','ventilation','petitdejeuner'
    ];


}
