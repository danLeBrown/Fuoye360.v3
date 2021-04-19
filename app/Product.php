<?php

namespace App;

use App\User;
use App\Wishlist;
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

    public function createProductData($product){
        $user_id = auth('sanctum')->user()->id;
        $user = User::select('name', 'image')->where('id', $product->user_id)->first();
        $product->user = $user;
        $product->price = number_format($product->price, 0, '.', ',');
        $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id' => $product->id])->get()) > 0 ? true : false;
        return $product;
    }
}
