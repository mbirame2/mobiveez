<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class boutique extends Model
{
    protected $table="showroom";
    protected $primaryKey = "idshowroom";
    public $timestamps = false;
    protected $fillable = [
      'etatshowroom','idmembre','id_dep', 'localisation','idcategorieshowroom','nomshowroom','descriptionshowroom','telephone','jourdebut','jourfin','heuredebut','heurefin','siteweb','logoshowroom','dateshowroom'
    ];
    public function user(){
      return $this->belongsTo('App\User','idmembre');
  }
}
