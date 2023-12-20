<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'api/delivery',
    'middleware' => 'cors',
 ],function($route){
    Route::get('/zone',  ['uses'=>'DeliveryController@getZone']); 
    Route::post('/tarificationZone',  ['uses'=>'DeliveryController@tarificationZone']); 

 });