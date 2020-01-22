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

/*Route::get('/', function () {
    return view('welcome');
});*/

//Route::get('/login', "homeController@index")->name('login');
//Route::post('/login-submit', 'homeController@login')->name('login-submit');
//Route::get('/logout', 'API\UserController@logout');

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('{path}',"HomeController@index")->where('path', '([A-z\d\-\/_.]+)?');

//Route::get('/home', 'HomeController@index')->name('home');
//Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');