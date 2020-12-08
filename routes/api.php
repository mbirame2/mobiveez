<?php

use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//retourne tous les endpoints commençant par /api/auth fait appel à AuthentificationController

    Route::get('/{photo}/{filename}',      ['as'=>'log','uses'=>'ApiController@images']);
    Route::post('/auth/login',  ['as'=>'login', 'middleware' => 'cors','uses'=>'AuthentificationController@login']);
    Route::post('/register',  ['as'=>'logi','uses'=>'AuthentificationController@register']);
    Route::get('/me',      ['as'=>'log','uses'=>'AuthentificationController@me']);
    Route::post('/logout', ['as'=>'lo','uses'=>'AuthentificationController@logout']);

    
 Route::get('error', function () {
    return response("Pas accés", 401);
   })->name('error');

//endpoint des particuliers
Route::group([
    'prefix'=>'part',
    'middleware' => 'cors',
    'middleware' => 'particulier:api',
 ],function($route){
     Route::post('/annonce',  ['as'=>'lo','uses'=>'ApiController@annonce']);
     Route::post('/commande_plat',  ['as'=>'lo','uses'=>'ApiController@commande_plat']);
     Route::get('/plat',  ['as'=>'lo','uses'=>'ApiController@getplat']);
     Route::get('/annonce',  ['as'=>'lo','uses'=>'ApiController@getannonce']);
     Route::get('/chambre',  ['as'=>'lo','uses'=>'ApiController@getchambre']); 
     Route::get('/vehicule',  ['as'=>'lo','uses'=>'ApiController@getvehicule']); 
     Route::get('/evenement',  ['as'=>'lo','uses'=>'ApiController@getevenement']); 

 });

//endpoint des professionnelles
Route::group([
    'prefix'=>'pro',
    'middleware' => 'cors',
    'middleware' => 'professionnel:api',
 ],function($route){
    Route::post('/annonce',  ['as'=>'lo','uses'=>'ApiController@annonce']);
     Route::post('/plat',  ['as'=>'lo','uses'=>'ApiController@plat']);
     Route::post('/chambre',  ['as'=>'lo','uses'=>'ApiController@chambre']); 
     Route::post('/vehicule',  ['as'=>'lo','uses'=>'ApiController@vehicule']); 
     Route::post('/evenement',  ['as'=>'lo','uses'=>'ApiController@evenement']); 


 });
