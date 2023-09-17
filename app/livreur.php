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
    protected $table="livreur";
    public $timestamps = false;
    protected $primaryKey = "idlivreur";
    protected $fillable = [
        'photolivreur', 'nomlivreur' ,'prenomlivreur','email','telephone','typelivreur','id_zone','permisconduire','cartedintentite','societe','registrecommerce'
    ];
}
