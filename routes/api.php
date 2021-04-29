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
   Route::get('/sendmail/{id}',  ['as'=>'lo','uses'=>'AuthentificationController@sendmail']); 
   Route::get('/getuser/{id}',  ['as'=>'lo','uses'=>'AuthentificationController@getuser']); 
   
   Route::post('/checkuser',  ['as'=>'lo','uses'=>'AuthentificationController@checkuser']); 
   Route::post('/contact',  ['as'=>'lo','uses'=>'AuthentificationController@contact']); 

   Route::post('/changepassword',  ['as'=>'lo','uses'=>'AuthentificationController@changepassword']); 
    Route::post('/login',  ['as'=>'login', 'middleware' => 'cors','uses'=>'AuthentificationController@login']);
    Route::post('/register',  ['as'=>'register','uses'=>'AuthentificationController@register']);
    Route::get('/me',      ['as'=>'log','uses'=>'AuthentificationController@me']);
    Route::post('/logout', ['as'=>'logout','uses'=>'AuthentificationController@logout']); 
    Route::post('/updateuser', ['as'=>'logout','uses'=>'AuthentificationController@updateuser']); 

});
Route::get('/departement/{id}',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@getdepartement']);
Route::get('/base',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@base']);
Route::get('/listemarque',      ['as'=>'log','middleware' => 'cors','uses'=>'EmarketController@listemarque']);

Route::get('/deleteimage/{filename}/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteimage']);
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
   Route::post('/offerarticle',  ['as'=>'lo','uses'=>'EmarketController@offerarticle']);

   
   Route::post('/buyboostarticle',  ['as'=>'lo','uses'=>'EmarketController@buyboostarticle']);

   Route::post('/addgestionnaire',  ['as'=>'lo','uses'=>'EmarketController@addgestionnaire']);
   Route::get('/listegestionnaire/{id}',  ['as'=>'lo','uses'=>'EmarketController@listegestionnaire']);
   Route::get('/deletegestionnaire/{id}',  ['as'=>'lo','uses'=>'EmarketController@deletegestionnaire']);
   Route::get('/gestionnaireshowroom/{id}',  ['as'=>'lo','uses'=>'EmarketController@gestionnaireshowroom']);

   Route::get('/onecommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@onecommande']);

   Route::post('/commandestatut',  ['as'=>'lo','uses'=>'EmarketController@commandestatut']);
   Route::post('/ajout_credit ',  ['as'=>'lo','uses'=>'EmarketController@ajout_credit']); 
   Route::post('/remove_credit ',  ['as'=>'lo','uses'=>'EmarketController@remove_credit']);
   Route::get('/oneannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneannonce']);
   Route::get('/deleteshowroom/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteshowroom']);
   Route::get('/deleteannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteannonce']);
   Route::get('/boostshowroom/{id}',  ['as'=>'lo','uses'=>'EmarketController@boostshowroom']);
   Route::get('/boostarticle/{id}',  ['as'=>'lo','uses'=>'EmarketController@boostarticle']);
   Route::get('/listoffer/{id}',  ['as'=>'lo','uses'=>'EmarketController@listoffer']);
   
   Route::get('/deleteoffer/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteoffer']);

   Route::get('/myoffers/{id}',  ['as'=>'lo','uses'=>'EmarketController@myoffers']);

   Route::post('/add_notification',  ['as'=>'lo','uses'=>'EmarketController@add_notification']);
   
   Route::post('/annoncesboutique',  ['as'=>'lo','uses'=>'EmarketController@annoncesboutique']);
   

   Route::get('/removenotification/{id}',  ['as'=>'lo','uses'=>'EmarketController@removenotification']);
   Route::get('/similarannonce/{name}',  ['as'=>'lo','uses'=>'EmarketController@similarannonce']);
    Route::post('/verify_contact',  ['as'=>'lo','uses'=>'EmarketController@verify_contact']);
    Route::get('/search_article/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_article']); 
    Route::get('/search_boutique/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_boutique']); 
    Route::get('/oneboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneboutique']);    
    Route::get('/proshowrooms/{id}',  ['as'=>'lo','uses'=>'EmarketController@showroomsuser']); 
    Route::get('/getboutique',  ['as'=>'lo','uses'=>'EmarketController@getboutique']); 
    Route::get('/getnotification',  ['as'=>'lo','uses'=>'EmarketController@getnotification']); 
    Route::get('/getarticleboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@getarticleboutique']); 
    Route::get('/ajout_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@ajout_panier']); 
    Route::get('/delete_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@delete_panier']); 
    Route::get('/liste_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@liste_panier']);  
    Route::get('/supprimercommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@supprimercommande']);
    Route::get('/getarticlevip',  ['as'=>'lo','uses'=>'EmarketController@getarticleservice']);
    Route::get('/getboutiquevip',  ['as'=>'lo','uses'=>'EmarketController@getboutiqueservice']);
    Route::get('/listeservice',  ['as'=>'lo','uses'=>'EmarketController@listeservice']);
    Route::get('/listecommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@listecommande']);
    
    Route::get('/listevente/{id}',  ['as'=>'lo','uses'=>'EmarketController@listevente']);

    
    Route::get('/payepourmoi/{id}',  ['as'=>'lo','uses'=>'EmarketController@payepourmoi']);
    Route::get('/statutcompte/{id}',  ['as'=>'lo','uses'=>'EmarketController@statutcompte']);
    Route::get('/getannonce',  ['as'=>'lo','uses'=>'EmarketController@allannonce']);
    Route::get('/liste_categorie',  ['as'=>'lo','uses'=>'EmarketController@liste_categorie']);
    Route::get('/liste_souscategorie',  ['as'=>'lo','uses'=>'EmarketController@liste_souscategorie']);
    Route::get('/gettransaction',  ['as'=>'lo','uses'=>'EmarketController@gettransaction']);
    Route::get('/proannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@proannonce']);


    ////favoris
    Route::get('/listefavoris/{id}',  ['as'=>'lo','uses'=>'EmarketController@listefavoris']);
    Route::get('/deletefavoris/{id}',  ['as'=>'lo','uses'=>'EmarketController@deletefavoris']);

    Route::post('/addfavoris',  ['as'=>'lo','uses'=>'EmarketController@addfavoris']);


});