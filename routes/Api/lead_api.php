<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/24/2018
 * Time: 11:20 AM
 */
Route::group(['prefix' => 'lead'], function (){
   Route::post('/update-status', [
       'uses' => 'Api\LeadController@updateStatus'
   ]);

    Route::post('/update', [
        'uses' => 'Api\LeadController@update'
    ]);

    Route::delete('/delete', [
        'uses' => 'Api\LeadController@delete'
    ]);

    Route::post('/listen-app', [
        'uses' => 'Api\LeadController@listenCallPasser'
    ]);

    Route::get('/listing', [
        'uses' => 'Api\LeadController@listing'
    ]);

    Route::get('/pivot', [
        'uses' => 'Api\LeadController@pivotData'
    ]);


});