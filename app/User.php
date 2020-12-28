<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable 
{
    use HasApiTokens, Notifiable;
    protected $table="membre";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'prenom','nom','password','departement_id','num_whatsapp','departement','telephoneportable','telephonefixe','email', 'localisation','etatcompte','compte','typecompte','sexe','codemembre','DateInscription','telephonefixe'
    ];
    public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function isRole(){
        return $this->typecompte;
    }
    public function departement(){
        return $this->belongsTo('App\departement','departement_id');
    }

}
