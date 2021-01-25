<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\DB;
use Auth;
use App\User;
use App\Product;
use App\Wishlist;
use App\Cart;
use App\Views;
use App\Notification;
use App\FollowingTable;
use Session;
use App\Subscription;
use App\ProductsView;
use App\ProductsImpression;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MadController extends Controller
{
    public function index()
    {
        $products = Product::all();
        foreach ($products as $key => $product) {
            if($product->views > 0){
                for ($i=0; $i < $product->views ; $i++) { 
                    $view = new ProductsView();
                    $view->user_id = 1;
                    $view->product_id = $product->id; 
                    $view->save();
                }
            }
        }
    }
}
