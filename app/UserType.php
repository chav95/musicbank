<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_type';

    public function user(){
        return $this->hasMany('App\User', 'privilege');
    }

    public function getTypeAttribute(){ //accessor
        return ucwords($this->attributes['type']);
    }
}
