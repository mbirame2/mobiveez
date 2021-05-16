<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{ 
    protected $table="oauth_access_tokens";
    public $timestamps = false;
    protected $primaryKey = "id";
    protected $fillable = [
        'user_id'
    ];
}
