<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaylistDetail extends Model
{
    public function playlist(){
        return $this->belongsTo('App\Playlist');
    }

    public function music(){
        return $this->belongsTo('App\Music');
    }
}
