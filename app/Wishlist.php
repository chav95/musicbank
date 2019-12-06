<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function detail(){
        return $this->hasMany('App\WishlistDetail', 'wishlist_id');
    }
}
