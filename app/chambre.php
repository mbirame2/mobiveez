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
    protected $primaryKey = "idchambre";

    public $timestamps = false;

    protected $fillable = [
       'prix','idhebergement','typechambre','capacite','description','typelit','climatisation','douche','baignoire','televiseur','refrigerateur','minibar','eauminerale','balcon','selectionfilm','conditionannulation','ventilation','petitdejeuner'
    ];


}
