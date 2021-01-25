<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table  = 'products';

    public function user()
    {
        # code...
        return $this->belongsTo('App\User');
    }
}
