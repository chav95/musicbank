<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    //Change Table Name
    //protected $table = 'playlists';
    //Set Primary Key
    //public $primaryKey = 'id';

    public function detail(){
        return $this->hasMany('App\PlaylistDetail');
    }

    public function playlistContent(){
        return $this->hasManyThrough('App\Music', 'App\PlaylistDetail', 'playlist_id');
    }

    public function parent(){
        return $this->hasOne('App\Playlist', 'id', 'parent_id')->where('status', '=', 1)->orderBy('nama_playlist');
    }
        
    public function children(){
        return $this->hasMany('App\Playlist', 'parent_id', 'id')->where('status', '=', 1)->orderBy('nama_playlist');
    }

    public function allChildren(){
        return $this->children()->with('allChildren');
    }

    public function music(){
        return $this->belongsToMany('App\Music');
    }

    public function childrenContent(){
        return $this->hasMany('App\Playlist', 'parent_id', 'id')->where('status', '=', 1)->orderBy('nama_playlist')->with('music');
    }

    public function allChildrenContent(){
        return $this->childrenContent()->with('allChildrenContent');
    }
        
    public static function tree(){
        return static::with(implode('.', array_fill(0, 100, 'children')))->where('parent_id', '=', '0')->where('status', '=', 1)->orderBy('nama_playlist')->get();
    }
}
