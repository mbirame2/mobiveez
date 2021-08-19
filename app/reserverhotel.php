<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reserverhotel extends Model
{
    
    protected $table="reserverhotel";
    protected $primaryKey = "idreservationhebergement";
    public $timestamps = false;
    protected $fillable = [
      'idmembre','idchambre','arrivee','depart','besoins','datereservation','statut','destinataire','motif','feedback','nombrenuitees'
    ];
}
