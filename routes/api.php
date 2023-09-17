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
Route::group([
   'prefix'=>'delivery',
   'middleware' => 'cors',
  // 'middleware' => 'auth:api',
],function($route){
   Route::get('/zone/{id_dept}',  ['uses'=>'DeliveryController@getZone']); 
   Route::post('/tarificationZone',  ['uses'=>'DeliveryController@tarificationZone']); 
   Route::post('/livreur',  ['uses'=>'DeliveryController@saveLivreur']); 
   Route::get('/getLivreur',  ['uses'=>'DeliveryController@getlivreur']); 
   Route::get('/getTarificationZone/{id}',  ['uses'=>'DeliveryController@getTarificationZone']); 
   Route::post('/deleteTarification',  ['uses'=>'DeliveryController@deleteTarification']); 
   Route::post('/deliver',  ['uses'=>'DeliveryController@deliver']); 
   Route::get('/deliver/{id}',  ['uses'=>'DeliveryController@getdeliver']); 

});
//retourne tous les endpoints commençant par /api/auth fait appel à AuthentificationController
Route::group([
   'prefix'=>'auth',
   'middleware' => 'cors',
],function($route){
      Route::get('/download/cgu/{version}',  ['as'=>'lo','uses'=>'ApiController@getDownload']); 
      Route::get('/updatemodule/{id}/{module}',  ['as'=>'lo','uses'=>'AuthentificationController@updatemodule']);

      Route::get('/sendmail/{id}/{lang}',  ['as'=>'lo','uses'=>'AuthentificationController@sendmail']); 
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
Route::get('/getidmembreofboutiqur',      ['uses'=>'AuthentificationController@getidmembreofboutiqur']);

Route::get('/departement/{id}',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@getdepartement']);
Route::get('/listetarificationlivraison',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@listetarificationlivraison']);

Route::get('/base',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@base']);
Route::get('/listemarque',      ['as'=>'log','middleware' => 'cors','uses'=>'EmarketController@listemarque']);

Route::get('/deleteimage/{filename}/{id}',  ['as'=>'lo','uses'=>'EmarketController@deleteimage']);
Route::get('/image/{filename}/{photo}',      ['as'=>'log','middleware' => 'cors','uses'=>'ApiController@images']);
Route::get('error', function () {
   return response()->json(['error'=>"Pas acces"], 401);            

   })->name('error');

Route::get('/listeservice/{id}',      ['middleware'=>'auth:api','middleware' => 'cors','uses'=>'ApiController@listeservice']);
Route::get('/getidservice/{id}',      ['middleware'=>'auth:api','middleware' => 'cors','uses'=>'ApiController@getidservice']);

Route::get('/getidservicewithmoduleonly/{id}',      ['middleware'=>'auth:api','middleware' => 'cors','uses'=>'ApiController@getidservicewithmoduleonly']);


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

    Route::post('/verify_contact',  ['as'=>'lo','uses'=>'EmarketController@verify_contact']);
    Route::get('/proshowrooms/{id}',  ['as'=>'lo','uses'=>'EmarketController@showroomsuser']); 
   Route::get('/ajout_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@ajout_panier']); 
    Route::get('/delete_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@delete_panier']); 
    Route::get('/liste_panier/{id}',  ['as'=>'lo','uses'=>'EmarketController@liste_panier']);  
    Route::get('/supprimercommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@supprimercommande']);
    Route::get('/listeservice',  ['as'=>'lo','uses'=>'EmarketController@listeservice']);
    Route::get('/listecommande/{id}',  ['as'=>'lo','uses'=>'EmarketController@listecommande']);
    
    Route::get('/listevente/{id}',  ['as'=>'lo','uses'=>'EmarketController@listevente']);

    
    Route::post('/payepourmoi',  ['as'=>'lo','uses'=>'EmarketController@payepourmoi']);
    Route::post('/filter_boutique',  ['as'=>'lo','uses'=>'EmarketController@filter_boutique']);

    Route::get('/statutcompte/{id}/{lang}',  ['as'=>'lo','uses'=>'EmarketController@statutcompte']);
    Route::get('/getannonce/{code}',  ['as'=>'lo','uses'=>'EmarketController@allannonce']);
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

   //Route::get('/getplat',  ['as'=>'lo','uses'=>'RestaurantController@getplat']);
   Route::get('/getrestaurant/{pays}',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurant']);
   Route::get('/mesrestaurants/{id}',  ['as'=>'lo','uses'=>'RestaurantController@mesrestaurants']);

 //  Route::get('/platrestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@platrestaurant']);
   
   Route::get('/supprimercommandeplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@supprimercommandeplat']);
   Route::get('/supprimerrestauration/{id}',  ['as'=>'lo','uses'=>'RestaurantController@supprimerrestauration']);

   Route::get('/oneplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@oneplat']);
   Route::get('/onerestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@onerestaurant']);
   Route::get('/listeservice',  ['as'=>'lo','uses'=>'RestaurantController@listeservice']);

   Route::get('/ajout_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@ajout_panier']); 
   Route::get('/delete_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@delete_panier']); 
   Route::get('/liste_panier/{id}',  ['as'=>'lo','uses'=>'RestaurantController@liste_panier']);  
   Route::get('/statutinvitation/{idreservation}/{idmembre}/{statut}',  ['as'=>'lo','uses'=>'RestaurantController@statutinvitation']);  
   
   Route::get('/listereservationid/{cle}/{valeur}',  ['as'=>'lo','uses'=>'RestaurantController@listereservationid']);  
   
   Route::get('/onereservationtable/{id}',  ['as'=>'lo','uses'=>'RestaurantController@onereservationtable']); 

   Route::get('/addinvitetable/{idreservation}/{idmembre}',  ['as'=>'lo','uses'=>'RestaurantController@addinvitetable']);  
   Route::get('/removemenuontable/{idreservation}',  ['as'=>'lo','uses'=>'RestaurantController@removemenuontable']);  
   Route::post('/addmenuontable',  ['as'=>'lo','uses'=>'RestaurantController@addmenuontable']);
   
   Route::post('/filter_restaurant',  ['as'=>'lo','uses'=>'RestaurantController@filter_restaurant']);

   Route::post('/commande_plat',  ['as'=>'lo','uses'=>'RestaurantController@commande_plat']);
   Route::get('/getplatvip',  ['as'=>'lo','uses'=>'RestaurantController@getplatservice']);
   Route::get('/getrestaurationvip',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurationservice']);
   Route::get('/typecuisine',  ['as'=>'lo','uses'=>'RestaurantController@typecuisine']);
   Route::post('/searchcategorieplat',  ['as'=>'lo','uses'=>'RestaurantController@searchcategorieplat']);
 
   Route::get('/search_plat/{pays}/{name}',  ['as'=>'lo','uses'=>'RestaurantController@searchplat']);
   Route::get('/search_restaurant/{pays}/{name}',  ['as'=>'lo','uses'=>'RestaurantController@searchrestaurant']); 
   Route::get('/deleteimage/{filename}/{id}',  ['as'=>'lo','uses'=>'RestaurantController@deleteimage']);
   Route::get('/boostplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@boostplat']);
   Route::get('/boostrestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@boostrestaurant']);
   Route::get('/deleteplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@deleteplat']);
   Route::get('/deleterestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@deleterestaurant']);
   Route::get('/bloquer_commande/{idannonce}/{statut}',  ['as'=>'lo','uses'=>'RestaurantController@bloquer_commande']);
   
   Route::get('/onecommandeplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@onecommandeplat']);

   Route::get('/listecommandeplat/{cle}/{valaur}',  ['as'=>'lo','uses'=>'RestaurantController@listecommandeplat']);
   Route::post('/buyboostrestauration',  ['as'=>'lo','uses'=>'RestaurantController@buyboostrestauration']);
   
   Route::post('/modifiercommandeplat',  ['as'=>'lo','uses'=>'RestaurantController@modifiercommandeplat']);

   Route::get('/listefavoris/{id}',  ['as'=>'lo','uses'=>'RestaurantController@listefavoris']);
   Route::get('/deletefavoris/{id}',  ['as'=>'lo','uses'=>'RestaurantController@deletefavoris']);

   Route::post('/addfavoris',  ['as'=>'lo','uses'=>'RestaurantController@addfavoris']);   
   
   Route::post('/statutreservationtable',  ['as'=>'lo','uses'=>'RestaurantController@statutreservationtable']);

   Route::post('/addgestionnaire',  ['as'=>'lo','uses'=>'RestaurantController@addgestionnaire']);
   Route::get('/listegestionnaire/{id}',  ['as'=>'lo','uses'=>'RestaurantController@listegestionnaire']);
   Route::get('/deletegestionnaire/{id}',  ['as'=>'lo','uses'=>'EmarketController@deletegestionnaire']);
   Route::get('/gestionnairerestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@gestionnairerestaurant']);
   Route::get('/gestionnaireconnected/{id}/{value}',  ['as'=>'lo','uses'=>'EmarketController@gestionnaireconnected']);


});



/*

  ////////  HOTEL  ////////////


 */

Route::group([
   'prefix'=>'hotel',
   'middleware' => 'cors',
   'middleware' => 'auth:api',
],function($route){

   Route::post('/chambre',  ['uses'=>'HotelController@chambre']);
   Route::post('/hebergement',  ['uses'=>'HotelController@hebergement']);
   Route::post('/reserverhotel',  ['uses'=>'HotelController@reserverhotel']);
   Route::post('/addfavoris',  ['uses'=>'HotelController@addfavoris']);
   Route::post('/statutreservation',  ['uses'=>'HotelController@statutreservation']);
   Route::post('/buyboostchambre',  ['uses'=>'HotelController@buyboosthotel']);
   Route::post('/buyboosthotel',  ['uses'=>'HotelController@buyboosthotel']);
   
   
//   Route::get('/getchambre',  ['uses'=>'HotelController@getchambre']);
  // Route::get('/gethotel',  ['uses'=>'HotelController@gethotel']);
   //Route::get('/onechambre/{id}',  ['uses'=>'HotelController@onechambre']);
   Route::get('/bloquer_reservation/{id}/{statut}',  ['uses'=>'HotelController@bloquer_reservation']);
   Route::get('/listefavoris/{id}',  ['uses'=>'HotelController@listefavoris']);
   Route::get('/listeservice',  ['uses'=>'HotelController@listeservice']);
   Route::get('/boostvip/{module}/{id}',  ['uses'=>'HotelController@boostvip']);
  // Route::get('/getchambreservice',  ['uses'=>'HotelController@getchambreservice']);
   //Route::get('/gethotelservice',  ['uses'=>'HotelController@gethotelservice']);

   Route::get('/meshotels/{id}',  ['uses'=>'HotelController@meshotels']);
   Route::get('/chambreshotel/{id}',  ['uses'=>'HotelController@chambreshotel']);
  // Route::get('/onehotel/{id}',  ['uses'=>'HotelController@onehotel']);
   Route::get('/deletefavoris/{id}',  ['uses'=>'HotelController@deletefavoris']);
   Route::get('/deleteimagechambre/{filename}/{id}',  ['uses'=>'HotelController@deleteimagechambre']);
   Route::get('/deleteimagehebergement/{filename}/{id}',  ['uses'=>'HotelController@deleteimagehebergement']);
   Route::get('/deletehotel/{id}',  ['uses'=>'HotelController@deletehotel']);
   Route::get('/deletechambre/{id}',  ['uses'=>'HotelController@deletechambre']);

   Route::get('/getreservationchambre/{id}',  ['uses'=>'HotelController@getreservationchambre']);
   
   Route::get('/reservationsrecues/{id}',  ['uses'=>'HotelController@reservationsrecues']);
   Route::get('/reservationsencours/{statut}/{id}',  ['uses'=>'HotelController@reservationsencours']);

   Route::get('/onereservationchambre/{id}',  ['uses'=>'HotelController@onereservationchambre']);
   
   Route::post('/filter_chambre',  ['uses'=>'HotelController@filter_chambre']);
  # Route::post('/filter_hotel',  ['uses'=>'HotelController@filter_hotel']);

   Route::post('/addgestionnaire',  ['uses'=>'HotelController@addgestionnaire']);
   Route::get('/listegestionnaire/{id}',  ['uses'=>'HotelController@listegestionnaire']);
   Route::get('/deletegestionnaire/{id}',  ['uses'=>'EmarketController@deletegestionnaire']);
   Route::get('/gestionnairehebergement/{id}',  ['uses'=>'HotelController@gestionnairehebergement']);
   Route::get('/gestionnaireconnected/{id}/{value}',  ['uses'=>'EmarketController@gestionnaireconnected']);

   Route::get('/search_hotel/{pays}/{name}',  [ 'uses'=>'HotelController@search_hotel']);
   Route::get('/search_chambre/{pays}/{name}',  [ 'uses'=>'HotelController@search_chambre']);

   
});



/*

  //////// FEATURES WITHOUT TOKEN  ////////////


 */


Route::group([
   'middleware' => 'cors',
],function($route){
   
   /// HOTEL //////
   Route::get('/hotel/onechambre/{id}',  ['uses'=>'HotelController@onechambre']);
   Route::get('/hotel/onehotel/{id}',  ['uses'=>'HotelController@onehotel']);
   Route::get('/hotel/getchambreservice/{pays}',  ['uses'=>'HotelController@getchambreservice']);
   Route::get('/hotel/gethotelservice/{pays}',  ['uses'=>'HotelController@gethotelservice']);
   Route::get('/hotel/getchambre/{pays}',  ['uses'=>'HotelController@getchambre']);
   Route::get('/hotel/gethotel/{code}',  ['uses'=>'HotelController@gethotel']);
   Route::get('/hotel/search_hotel/{pays}/{name}',  [ 'uses'=>'HotelController@search_hotel']);
   Route::get('/hotel/search_chambre/{pays}/{name}',  [ 'uses'=>'HotelController@search_chambre']);
   Route::post('/hotel/filter_chambre',  ['uses'=>'HotelController@filter_chambre']);
   Route::post('/hotel/filter_hotel',  ['uses'=>'HotelController@filter_hotel']);
   Route::get('/hotel/chambreshotel/{id}',  ['uses'=>'HotelController@chambreshotel']);

  
  // RESTAURANT/
   Route::get('/restaurant/mesrestaurants/{id}',  ['as'=>'lo','uses'=>'RestaurantController@mesrestaurants']);
   Route::get('/restaurant/categorie_restaurant/{lang}',  ['as'=>'lo','uses'=>'RestaurantController@categorie_restaurant']);
   Route::get('/restaurant/categorie_plat/{lang}',  ['as'=>'lo','uses'=>'RestaurantController@categorie_plat']);
   Route::get('/restaurant/getplat/{pays}',  ['as'=>'lo','uses'=>'RestaurantController@getplat']);
   Route::get('/restaurant/getrestaurant/{pays}',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurant']);
   Route::get('/restaurant/oneplat/{id}',  ['as'=>'lo','uses'=>'RestaurantController@oneplat']);
   Route::get('/restaurant/onerestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@onerestaurant']);
   Route::get('/restaurant/search_plat/{pays}/{name}',  ['as'=>'lo','uses'=>'RestaurantController@searchplat']);
   Route::get('/restaurant/search_restaurant/{pays}/{name}',  ['as'=>'lo','uses'=>'RestaurantController@searchrestaurant']); 
   Route::post('/restaurant/filter_restaurant',  ['as'=>'lo','uses'=>'RestaurantController@filter_restaurant']);
   Route::get('/restaurant/getplatvip/{pays}',  ['as'=>'lo','uses'=>'RestaurantController@getplatservice']);
   Route::get('/restaurant/getrestaurationvip/{pays}',  ['as'=>'lo','uses'=>'RestaurantController@getrestaurationservice']);
   Route::get('/restaurant/searchcategorieplat/{pays}/{name}',  ['as'=>'lo','uses'=>'RestaurantController@searchcategorieplat']);
   Route::get('/restaurant/platrestaurant/{id}',  ['as'=>'lo','uses'=>'RestaurantController@platrestaurant']);
   Route::post('/restaurant/searchcategorieplat',  ['as'=>'lo','uses'=>'RestaurantController@searchcategorieplat']);


   //////// E-MARKET////////////
   Route::post('/emarket/filter_article',  ['as'=>'lo','uses'=>'EmarketController@filter_article']);
   Route::post('/emarket/filter_boutique',  ['as'=>'lo','uses'=>'EmarketController@filter_boutique']);
   Route::get('/emarket/oneannonce/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneannonce']);
   Route::get('/emarket/similarannonce/{pays}/{name}',  ['as'=>'lo','uses'=>'EmarketController@similarannonce']);
   Route::get('/emarket/search_article/{pays}/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_article']); 
   Route::get('/emarket/search_boutique/{pays}/{name}',  ['as'=>'lo','uses'=>'EmarketController@search_boutique']); 
   Route::get('/emarket/oneboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@oneboutique']);    
   Route::get('/emarket/getboutique/{code}',  ['as'=>'lo','uses'=>'EmarketController@getboutique']); 
   Route::get('/emarket/getarticleboutique/{id}',  ['as'=>'lo','uses'=>'EmarketController@getarticleboutique']); 
   Route::get('/emarket/getarticlevip/{code}',  ['as'=>'lo','uses'=>'EmarketController@getarticleservice']);
   Route::get('/emarket/getboutiquevip/{code}',  ['as'=>'lo','uses'=>'EmarketController@getboutiqueservice']);
   Route::get('/emarket/getannonce/{code}',  ['as'=>'lo','uses'=>'EmarketController@allannonce']);
   Route::get('/emarket/liste_categorie',  ['as'=>'lo','uses'=>'EmarketController@liste_categorie']);
   Route::get('/emarket/detailsoffer/{id}',  ['as'=>'lo','uses'=>'EmarketController@detailsoffer']);




});