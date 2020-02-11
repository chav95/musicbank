<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Music extends Model
{
    public function log(){
        return $this->hasMany('App\Log');
    }

    /*public function playlistDetail(){
        return $this->hasMany('App\PlaylistDetail');
    }

    public function belongToPlaylist(){
        return $this->hasManyThrough('App\Playlist', 'App\PlaylistDetail');
    }*/

    public function playlist(){
        return $this->belongsToMany('App\Playlist');
    }
    
    public function getFilenameAttribute(){ //accessor
        $filename = str_replace(" ", "%20", str_replace("#", "%23", $this->attributes['filename']));
        return 'http://172.18.11.32:8082/aud_uploads/'.$filename;
        
        //return url('/storage/uploadedMusic/'.$this->attributes['filename']);
        //return Storage::disk('public')->path('uploadedMusic/'.$this->attributes['filename']);
    }
}
