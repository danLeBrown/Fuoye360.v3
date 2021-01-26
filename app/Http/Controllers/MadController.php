<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Cart;
use App\User;
use App\Views;
use App\Product;
use App\Wishlist;
use App\Notification;
use App\ProductsView;
use App\Subscription;
use App\FollowingTable;
use App\ProductsImpression;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ShopResource;
use Illuminate\Pagination\Paginator;
use Intervention\Image\Facades\Image;
use Illuminate\Pagination\LengthAwarePaginator;

class MadController extends Controller
{
    public function index()
    {
        $products = Product::all();
        foreach ($products as $key => $product) {
            list($width, $height) = getimagesize(storage_path('app/public/product_images/'.$product->image));
            //obtain ratio
            $imageratio = $width/$height;

            if($imageratio >= 1){
                $newwidth = 600;
                $newheight = 600 / $imageratio; 
            }
            else{
                $newwidth = 400;
                $newheight = 400 / $imageratio;
            };
            $img = Image::make(storage_path('app/public/product_images/'.$product->image))->resize($newwidth, $newheight)->save(storage_path('app/public/product_images/'.$product->image));
        }
    }
}
