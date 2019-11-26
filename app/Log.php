<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    public function music(){
        return $this->belongsTo('App\Music');
    }
}
