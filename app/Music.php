<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    public function log(){
        return $this->hasMany('App\Log');
    }

    public function playlistDetail(){
        return $this->hasMany('App\PlaylistDetail');
    }

    public function belongToPlaylist(){
        return $this->hasManyThrough('App\Playlist', 'App\PlaylistDetail');
    }
}
