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

//Route::get('/login', "homeController@index")->name('login');
//Route::post('/login-submit', 'homeController@login')->name('login-submit');

//Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/manage-users', 'HomeController@index')->middleware('can:isAdmin');
Route::get('{path}',"HomeController@index")->where('path', '([A-z\d\-\/_.]+)?');

//Route::get('/home', 'HomeController@index')->name('home');
//Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');