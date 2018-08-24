<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//
//    return view('welcome');
//});

Route::get('/', [
    'as' => 'welcome',
    'uses' => 'WelcomeController@index'
]);


Route::get('/test', [
    'as' => 'app.test',
    'uses' => 'Backend\AppController@test'
]);

Route::get('/update', [
    'as' => 'app.update',
    'uses' => 'Backend\AppController@update'
]);


/*--------BackEND router-----------*/
require(__DIR__ . "/Backend/Backend.php");

require(__DIR__ . "/Frontend/Frontend.php");




Route::match(['get', 'post'], '/backend/listen', [
    'as' => 'backend.app.listen',
    'uses' => 'Backend\AppController@listen'
]);

/** -----API----- */
Route::group(['prefix' => 'api'], function (){
    require ('Api/lead_api.php');
});

Auth::routes();
Route::get('/auth/logout', function (){
    Auth::logout();
});


Route::get('/home', 'HomeController@index')->name('home');
