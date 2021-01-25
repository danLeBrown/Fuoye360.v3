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

class ActionController extends Controller
{
    public function addToWishlist($product_id)
    {
        $user_id = auth('sanctum')->user()->id;
        if(\is_numeric($product_id)){
            $checkWishlist = Wishlist::where(['user_id' => $user_id, 'product_id' => $product_id])->get();
            $status = 500;
            if(count($checkWishlist) <= 0){
                $wishlist = new Wishlist;
                $wishlist->product_id = $product_id;
                $wishlist->user_id = $user_id;
                $wishlist->save();

                $status = 200;
            }
            $data = array(
                'status' => $status
            );

            return new ShopResource($data);

        }
    }

    public function updateViews($product_id)
    {
        $views = count(ProductsView::where('product_id', $product_id)->get());
        $status = 500;
        $user_id = auth('sanctum')->user()->id;
        if(\is_numeric($product_id)){
            $seller_id = Product::find($product_id)->user_id;
            if($user_id == $seller_id){
            }else{
                $status = 200;
                $sessionProductViews = Session::has('productViews') ? Session::get('productViews') : null;
                if ($sessionProductViews !== null) {
                    $array_product_id = array_column($sessionProductViews, "product_id");
                    if (in_array($product_id, $array_product_id)) {
                        $views = count(ProductsView::where('product_id', $product_id)->get());
                    }else{
                        $productToAdd = array(
                            'product_id' => $product_id
                        );
                        $newProductView = new ProductsView();
                        $newProductView->user_id = $user_id;
                        $newProductView->product_id = $product_id;
                        $newProductView->save();
                        $views = count(ProductsView::where('product_id', $product_id)->get());
                        Session::push('productViews', $productToAdd);
                    }
                }else {
                    $newProductView = new ProductsView();
                    $newProductView->user_id = $user_id;
                    $newProductView->product_id = $product_id;
                    $newProductView->save();
                    $views = count(ProductsView::where('product_id', $product_id)->get());

                    $productToAdd = array(
                        'product_id' => $product_id
                    );
                    Session::put('productViews', []);
                    Session::push('productViews', $productToAdd);
                }

            }
            $countSessionProductViews = Session::get('productViews');

            $data = array(
                'status' => $status,
                'views' => $views.' views'
            );
            return new ShopResource($data);

        }
    }

    public function removeFromWishlist($product_id)
    {
        $user_id = auth('sanctum')->user()->id;
        if (is_numeric($product_id)) {
            $checkWishlist = Wishlist::where(['user_id' => $user_id, 'product_id' => $product_id])->get();
            $status = 500;
            if(count($checkWishlist) > 0){
                $checkWishlist[0]->delete();
                $status = 200;
            }
            $data = array(
                'status' => $status
            );
            return new ShopResource($data);

        }
    }

    public function addToCart($product_id, $qty)
    {
        if (is_numeric($product_id) && \is_numeric($qty)) {
            $findProduct = Product::find($product_id);
            $findSeller = User::find($findProduct->user_id);
            $oldCart = Session::has('shoppingCart') ? Session::get('shoppingCart') : null;
            $cart = new Cart($oldCart);
            $tel = $findSeller->telephone;
            $tel = substr($tel, 1);
            $findSeller->telephone = "234".$tel;
            $product = array(
                'id'=> $findProduct->id,
                'qty'=>$qty,
                'seller_id' => $findProduct->user_id,
                'seller_name' => $findSeller->name,
                'seller_image'=>$findSeller->image,
                'seller_location'=>$findSeller->geo_location,
                'seller_telephone' => $findSeller->telephone
            );
              
            $cart->addToCart($findProduct, $product);
    
            Session::put('shoppingCart', $cart);
    
            $data = array(
                'status' => 200,
                'cart' => Session::get('shoppingCart')
            );
            return new ShopResource($data);
        }
        
    }
    public function removeSellerFromCart($seller_id)
    {
        $user_id = auth('sanctum')->user()->id;

        if (is_numeric($seller_id)) {
            $oldCart = Session::has('shoppingCart') ? Session::get('shoppingCart') : null;
            $status =500;
            $session_cart = $oldCart;
            if($oldCart != null){
                $status =200;
                $session_cart = new Cart($oldCart);
                
                $session_cart->removeSeller($seller_id);
                $session_cart->filterCart();
                Session::put('shoppingCart', $session_cart);
    
                $shopping_cart = $session_cart->filter;
                foreach ($shopping_cart as $key => $cart) {
                    $items = $cart['products'];
                    foreach ($items as $key => $item) {
                        if (is_numeric($item['price'])) {
                            $item['price'] = number_format($item['price'], 0, '.', ',');
                        }
                        if (is_numeric($item['item']->price)) {
                            $item['item']->price = number_format($item['item']->price, 0, '.', ',');
                        }
                        $item['item']->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $item['item']->id])->get()) > 0 ? true: false;
                    }
                }
            }
    
            $data = array(
                'shopping_cart' => $session_cart,
                'status' => $status
            );
            return new ShopResource($data);

        }
    }

    public function removeProductFromCart($product_id, $seller_id)
    {
        $user_id = auth('sanctum')->user()->id;

        if (is_numeric($product_id) && \is_numeric($seller_id)) {
            $oldCart = Session::has('shoppingCart') ? Session::get('shoppingCart') : null;
            $status =500;
            $session_cart = $oldCart;
            if($oldCart != null){
                $status =200;
                $session_cart = new Cart($oldCart);
                
                $session_cart->removeProduct($product_id, $seller_id);
                $session_cart->filterCart();
                Session::put('shoppingCart', $session_cart);
                $shopping_cart = $session_cart->filter;
                foreach ($shopping_cart as $key => $cart) {
                    $items = $cart['products'];
                    foreach ($items as $key => $item) {
                        if (is_numeric($item['price'])) {
                            $item['price'] = number_format($item['price'], 0, '.', ',');
                        }
                        if (is_numeric($item['item']->price)) {
                            $item['item']->price = number_format($item['item']->price, 0, '.', ',');
                        }
                        $item['item']->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $item['item']->id])->get()) > 0 ? true: false;
                    }
                }
            }

            $data = array(
                'shopping_cart' => $session_cart,
                'status' => $status
            );
            return new ShopResource($data);

        }
    }

    public function productImpression($product_id, $type)
    {
        $user_id = auth('sanctum')->user()->id;

        if (is_numeric($product_id) && \is_string($type)) {
            $checkProduct = Product::where('id', $product_id)->get();             
            $status = 500;
            $productImpressions = 0;
            if(count($checkProduct) > 0){
                if($checkProduct[0]->user_id != $user_id){
                    $status = 200;
                    $sessionProductImpressions = Session::has('productImpressions') ? Session::get('productImpressions') : null;
                     if ($sessionProductImpressions !== null) {
                        $array_product_id = array_column($sessionProductImpressions, "product_id");
                        if (!in_array($product_id, $array_product_id)) {
                            $productToAdd = array(
                                'product_id' => $product_id
                            );

                            $newProductImpression = new ProductsImpression;
                            $newProductImpression->product_id = $product_id;
                            $newProductImpression->user_id = $user_id;   
                            $newProductImpression->save();

                            Session::push('productImpressions', $productToAdd);
                        }
                    }else {
                        $newProductImpression = new ProductsImpression;
                        $newProductImpression->product_id = $product_id;
                        $newProductImpression->user_id = $user_id;   
                        $newProductImpression->save();

                        $productToAdd = array(
                            'product_id' => $product_id
                        );
                        Session::put('productImpressions', []);
                        Session::push('productImpressions', $productToAdd);
                    }
                }
            }
            $productImpressions = count(ProductsImpression::where('product_id', $product_id)->get());
            $data = array(
                'status' => $status,
                'product_impressions' => $productImpressions
            );
            return new ShopResource($data);

        }
    }

    public function contactSeller($seller_id)
    {
        $user_id = auth('sanctum')->user()->id;

        if (is_numeric($seller_id)) {
            $oldCart = Session::has('shoppingCart') ? Session::get('shoppingCart') : null;
            $status =500;
            $session_cart = $oldCart;
            if($oldCart != null){
                $status =200;
                $session_cart = new Cart($oldCart);
                $session_cart->contactSeller($seller_id);
                $session_cart->filterCart();
                $checkout = $session_cart->checkout;
                $notification = new Notification();
                $notification->sender_id = $user_id;
                $notification->receiver_id = $seller_id;
                // $notification->data = [];
                $notification->type = 'sales';
                $data = array();
                foreach ($checkout as $key => $product) {
                    $append  = [
                        'qty'=> $product['qty'],
                        'id' => $product['item']->id,
                        'name' => $product['item']->name,
                    ];
                    array_push($data, $append);
                }
                $notification->data = \json_encode($data);
                $notification->save();
                Session::put('shoppingCart', $session_cart);
                $shopping_cart = $session_cart->filter;
                foreach ($shopping_cart as $key => $cart) {
                    $items = $cart['products'];
                    foreach ($items as $key => $item) {
                        if (is_numeric($item['price'])) {
                            $item['price'] = number_format($item['price'], 0, '.', ',');
                        }
                        if (is_numeric($item['item']->price)) {
                            $item['item']->price = number_format($item['item']->price, 0, '.', ',');
                        }
                        $item['item']->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $item['item']->id])->get()) > 0 ? true: false;
                    }
                }
            }
        }
        $data = array(
            'shopping_cart' => $session_cart,
            'status' => $status
        );
        return new ShopResource($data);
   
    }
}
