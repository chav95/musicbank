<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MusicPlaylist extends Model
{
    protected $table = 'music_playlist';

    public function playlist(){
        return $this->belongsTo('App\Playlist');
    }

    public function music(){
        return $this->belongsTo('App\Music');
    }
}
