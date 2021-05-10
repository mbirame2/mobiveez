<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class annonce extends Model
{
    protected $table="annonce";
    protected $primaryKey = "idannonce";
    protected $fillable = [
       'idmembre','id_dep','statutvente','referenceannonce',"troc",'paiementtranche','nombretranche','delaipaiementtranche','photo','idsouscategorie','typeannonce','prix','localisation','statut','validite','ville','titre','description','dateannonce','datevente','statutvente','nomvendeur','coutrevient' ,'bloquer_commande'
    ];
    public $timestamps = false;
    public function departement(){
        return $this->belongsTo('App\departement','id_dep');
    }
  
}
