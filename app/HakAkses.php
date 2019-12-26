<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    public function user(){
        return $this->hasMany('App\User', 'hak_akses');
    }

    public function getTypeAttribute(){ //accessor
        return ucwords($this->attributes['type']);
    }
}
