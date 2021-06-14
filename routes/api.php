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
 

 /*

  ////////  E MARKET  ////////////


 */

 Route::group([
   'prefix'=>'emarket',
   'middleware' => 'cors',
   'middleware' => 'auth:api',
],function($route){
   Route::post('/annonce',  ['as'=>'lo','uses'=>'EmarketController@annonce']);
   
   Route::post('/updateannonce',  ['as'=>'lo','uses'=>'EmarketController@updateannonce']);

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
   Route::post('/statutoffer',  ['as'=>'lo','uses'=>'EmarketController@statutoffer']);
   Route::get('/deleteoffer/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteoffer']);
   Route::get('/getusercredit/{id}',  ['as'=>'lo','uses'=>'EmarketController@getusercredit']);

   Route::get('/bloquer_commande/{idannonce}/{statut}',  ['as'=>'lo','uses'=>'EmarketController@bloquer_commande']);
   
   Route::get('/gestionnaireconnected/{id}/{value}',  ['as'=>'lo','uses'=>'EmarketController@gestionnaireconnected']);

   Route::get('/myoffers/{id}',  ['as'=>'lo','uses'=>'EmarketController@myoffers']);

   
   Route::post('/annoncesboutique',  ['as'=>'lo','uses'=>'EmarketController@annoncesboutique']);

   Route::post('/add_notification',  ['as'=>'lo','uses'=>'EmarketController@add_notification']);
   Route::get('/removenotification/{id}',  ['as'=>'lo','uses'=>'EmarketController@removenotification']);
   Route::get('/getnotification/{id}/{module}',  ['as'=>'lo','uses'=>'EmarketController@getnotification']); 

   Route::get('/similarannonce/{name}',  ['as'=>'lo','uses'=>'EmarketController@similarannonce']);
    Route::post('/verify_contact',  ['as'=>'lo','uses'=>'EmarketController@verify_contact']);
    Route::get('/search_article/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_article']); 
    Route::get('/search_boutique/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_boutique']); 
    Route::get('/oneboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneboutique']);    
    Route::get('/proshowrooms/{id}',  ['as'=>'lo','uses'=>'EmarketController@showroomsuser']); 
    Route::get('/getboutique',  ['as'=>'lo','uses'=>'EmarketController@getboutique']); 
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

    
    Route::post('/payepourmoi',  ['as'=>'lo','uses'=>'EmarketController@payepourmoi']);
    Route::post('/filter_boutique',  ['as'=>'lo','uses'=>'EmarketController@filter_boutique']);

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

/////////////////////////////////////////////////////////////


/*

  ////////  RESTAURANT  ////////////


 */

Route::group([
   'prefix'=>'restaurant',
   'middleware' => 'cors',
   'middleware' => 'auth:api',
],function($route){
   Route::post('/plat',  ['as'=>'lo','uses'=>'RestaurantController@plat']);
   Route::post('/restauration',  ['as'=>'lo','uses'=>'RestaurantController@restauration']);
   Route::post('/reservationtable',  ['as'=>'lo','uses'=>'RestaurantController@reservationtable']);
   
   Route::get('/listereservationtable/{id}',  ['as'=>'lo','uses'=>'RestaurantController@listereservationtable']);

   Route::get('/getplat',  ['as'=>'lo','uses'=>'RestaurantController@getplat']);
   Route::get('/getrestaurant',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurant']);
   Route::get('/mesrestaurants/{id}',  ['as'=>'lo','uses'=>'RestaurantController@mesrestaurants']);

   Route::get('/platrestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@platrestaurant']);
   Route::get('/getplatvip',  ['as'=>'lo','uses'=>'RestaurantController@getplatvip']);
   Route::get('/getrestaurationvip',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurationvip']);

   Route::get('/oneplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@oneplat']);
   Route::get('/onerestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@onerestaurant']);
   Route::get('/listeservice',  ['as'=>'lo','uses'=>'RestaurantController@listeservice']);

   Route::get('/ajout_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@ajout_panier']); 
   Route::get('/delete_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@delete_panier']); 
   Route::get('/liste_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@liste_panier']);  
   Route::get('/declineinvitation/{idreservation}/{idmembre}',  ['as'=>'lo','uses'=>'RestaurantController@declineinvitation']);  
   
   Route::get('/getplatvip',  ['as'=>'lo','uses'=>'RestaurantController@getplatservice']);
   Route::get('/getrestaurationvip',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurationservice']);

   
   Route::get('/listefavoris/{id}',  ['as'=>'lo','uses'=>'RestaurantController@listefavoris']);
   Route::get('/deletefavoris/{id}',  ['as'=>'lo','uses'=>'RestaurantController@deletefavoris']);

   Route::post('/addfavoris',  ['as'=>'lo','uses'=>'RestaurantController@addfavoris']);

});

