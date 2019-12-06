<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishlistDetail extends Model
{
    public function wishlist(){
        return $this->belongsTo('App\Wishlist');
    }

    public function music(){
        return $this->belongsTo('App\Music');
    }
}
