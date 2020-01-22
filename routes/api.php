<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resources([    
    'music' => 'API\MusicController',
    'playlist' => 'API\PlaylistController',
    'log' => 'API\LogController',
    'wishlist' => 'API\WishlistController',
    'user' => 'API\UserController',
    'user_type' => 'API\UserTypeController',
    'hak_akses' => 'API\HakAksesController',
    'sendmail' => 'API\SendMailController',
    /*'sendmail' => function(\Illuminate\Http\Request $request, \Illuminate\Mail\Mailer $mailer){
        $mailer->to('chavinpradana@gmail.com')->send(new \App\Mail\SendMail($request));
    }*/
]);

/*Route::post('/api/sendmail', function(\Illuminate\Http\Request $request, \Illuminate\Mail\Mailer $mailer){
    $mailer->to('chavinpradana@gmail.com')->send(new \App\Mail\SendMail($request));
});*/
Route::get('/api/sendmail', 'API\SendMailController@mail')->name('sendMail');