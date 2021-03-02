<?php


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
Route::group([
   'prefix'=>'auth',
   'middleware' => 'cors',
],function($route){
    Route::post('/login',  ['as'=>'login', 'middleware' => 'cors','uses'=>'AuthentificationController@login']);
    Route::post('/register',  ['as'=>'register','uses'=>'AuthentificationController@register']);
    Route::get('/me',      ['as'=>'log','uses'=>'AuthentificationController@me']);
    Route::post('/logout', ['as'=>'logout','uses'=>'AuthentificationController@logout']); 
    Route::post('/updateuser', ['as'=>'logout','uses'=>'AuthentificationController@updateuser']); 

});
Route::get('/departement/{id}',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@getdepartement']);
Route::get('/base',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@base']);
Route::get('/image/{filename}/{photo}',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@images']);
 Route::get('error', function () {
    return response()->json(['error'=>"Pas acces"], 401);            

   })->name('error');

//endpoint des particuliers
Route::group([
    'prefix'=>'part',
    'middleware' => 'cors',
    'middleware' => 'auth:api',
 ],function($route){
     Route::post('/annonce',  ['as'=>'lo','uses'=>'ApiController@annonce']);
     Route::post('/commande_plat',  ['as'=>'lo','uses'=>'ApiController@commande_plat']);
     Route::get('/getplat',  ['as'=>'lo','uses'=>'ApiController@getplat']);
    
     Route::get('/chambre',  ['as'=>'lo','uses'=>'ApiController@getchambre']); 
     Route::get('/vehicule',  ['as'=>'lo','uses'=>'ApiController@getvehicule']); 
     Route::get('/evenement',  ['as'=>'lo','uses'=>'ApiController@getevenement']); 
 });

//endpoint des professionnelles
Route::group([
    'prefix'=>'pro',
    'middleware' => 'cors',
    'middleware' => 'auth:api',
 ],function($route){
    Route::post('/annonce',  ['as'=>'lo','uses'=>'ApiController@annonce']);
     Route::post('/plat',  ['as'=>'lo','uses'=>'ApiController@plat']);
     Route::post('/chambre',  ['as'=>'lo','uses'=>'ApiController@chambre']); 
     Route::post('/vehicule',  ['as'=>'lo','uses'=>'ApiController@vehicule']); 
     Route::post('/evenement',  ['as'=>'lo','uses'=>'ApiController@evenement']); 


 });
 Route::group([
    'prefix'=>'backoffice',
    'middleware' => 'cors',
    'middleware' => 'auth:api',
 ],function($route){
    Route::get('/annonce/{id}',  ['as'=>'lo','uses'=>'ApiController@validerannonce']);
     Route::post('/plat',  ['as'=>'lo','uses'=>'ApiController@validerplat']);
     Route::post('/chambre',  ['as'=>'lo','uses'=>'ApiController@validerchambre']); 
     Route::post('/vehicule',  ['as'=>'lo','uses'=>'ApiController@validervehicule']); 
     Route::post('/evenement',  ['as'=>'lo','uses'=>'ApiController@validerevenement']); 

 });
 
 ////////e-market////////////

 Route::group([
   'prefix'=>'emarket',
   'middleware' => 'cors',
   'middleware' => 'auth:api',
],function($route){
   Route::post('/annonce',  ['as'=>'lo','uses'=>'EmarketController@annonce']);
   Route::post('/imageprofil',  ['as'=>'lo','uses'=>'EmarketController@imageprofil']);
   Route::post('/commander',  ['as'=>'lo','uses'=>'EmarketController@commander']);
   Route::post('/filter_article',  ['as'=>'lo','uses'=>'EmarketController@filter_article']);
   Route::post('/modifiercommande',  ['as'=>'lo','uses'=>'EmarketController@modifiercommande']);
   Route::post('/boutique',  ['as'=>'lo','uses'=>'EmarketController@boutique']);
   Route::post('/ajout_credit ',  ['as'=>'lo','uses'=>'EmarketController@ajout_credit']); 
   Route::post('/remove_credit ',  ['as'=>'lo','uses'=>'EmarketController@remove_credit']);
   Route::get('/oneannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneannonce']);
   Route::get('/removenotification/{id}',  ['as'=>'lo','uses'=>'EmarketController@removenotification']);
   Route::get('/similarannonce/{name}',  ['as'=>'lo','uses'=>'EmarketController@similarannonce']);
    Route::get('/verify_contact/{numero}',  ['as'=>'lo','uses'=>'EmarketController@verify_contact']);
    Route::get('/search_article/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_article']); 
    Route::get('/search_boutique/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_boutique']); 
    Route::get('/oneboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneboutique']); 
    Route::get('/getboutique',  ['as'=>'lo','uses'=>'EmarketController@getboutique']); 
    Route::get('/getnotification',  ['as'=>'lo','uses'=>'EmarketController@getnotification']); 
    Route::get('/getarticleboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@getarticleboutique']); 
    Route::get('/ajout_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@ajout_panier']); 
    Route::get('/delete_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@delete_panier']); 
    Route::get('/liste_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@liste_panier']);  
    Route::get('/supprimercommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@supprimercommande']);
    Route::get('/getarticlevip',  ['as'=>'lo','uses'=>'EmarketController@getarticleservice']);
    Route::get('/listeservice',  ['as'=>'lo','uses'=>'EmarketController@listeservice']);
    Route::get('/listecommande',  ['as'=>'lo','uses'=>'EmarketController@listecommande']);
    Route::get('/payepourmoi/{id}',  ['as'=>'lo','uses'=>'EmarketController@payepourmoi']);
    Route::get('/statutcompte/{id}',  ['as'=>'lo','uses'=>'EmarketController@statutcompte']);
    Route::get('/getannonce',  ['as'=>'lo','uses'=>'EmarketController@allannonce']);
    Route::get('/gettransaction',  ['as'=>'lo','uses'=>'EmarketController@gettransaction']);
    Route::get('/proannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@proannonce']);
});