<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    public function userType(){
        return $this->belongsTo('App\UserType', 'user_type');
    }
    public function hakAkses(){
        return $this->belongsTo('App\HakAkses', 'hak_akses');
    }
    public function wishlistCreated(){
        return $this->hasMany('App\Wishlist', 'user_id');
    }
    public function musicUploaded(){
        return $this->hasMany('App\Music', 'uploaded_by');
    }
    public function playlistCreated(){
        return $this->hasMany('App\Playlist', 'created_by');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'user_type', 'hak_akses',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
