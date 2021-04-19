<?php

namespace App\Http\Controllers;

use Session;
use App\Cart;
use App\Post;
use App\User;
use DateTime;
use App\Product;
use App\Wishlist;
use App\PostsLike;
use App\Notification;
use App\PostsRetweet;
use App\ProductsView;
use App\PostsBookmark;
use App\FollowingTable;
use App\ProductsImpression;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session as Session;
use App\Mail\SendFeedbackMail;
use App\Events\NewNotification;
use Illuminate\Support\Collection;
use App\Http\Resources\ShopResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

class ActionController extends Controller
{
    public function updateTime($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time    = time();
        $time_difference = $current_time - $time_ago;
        $seconds         = $time_difference;

        $minutes = round($seconds / 60); // value 60 is seconds
        $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
        $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;
        $weeks   = round($seconds / 604800); // 7*24*60*60;
        $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60
        $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

        if ($seconds <= 60) {

            return "1s";
        } else if ($minutes <= 60) {

            if ($minutes == 1) {

                return "1m";
            } else {

                return $minutes . "m";
            }
        } else if ($hours <= 24) {

            if ($hours == 1) {

                return "1hr";
            } else {

                return $hours . "hrs";
            }
        } else if ($days <= 7) {

            if ($days == 1) {

                return "1d";
            } else {

                return $days . "d";
            }
        }
        /* 
            else if ($weeks <= 4.3){

                if ($weeks == 1){

                    return "a week ago";

                } else {

                    return "$weeks weeks ago";

                }

            } else if ($months <= 12){

                if ($months == 1){

                    return "a month ago";

                } else {

                    return "$months months ago";

                }

            } else {

                if ($years == 1){

                    return "1 year ago";

                } else {

                    return "$years years ago";

                }
            */ else {
            // return getdate($time_ago);
            return getdate($time_ago)['mday'] . " " . substr(getdate($time_ago)['month'], 0, 3) . " '" . substr(getdate($time_ago)['year'], 2, 4);
        }
    }

    public function checkForLinks($text)
    {
        // The Regular Expression filter
        // $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        // $reg_exUrl = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";
        $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";


        // The Text you want to filter for urls
        // $text = "The text you want to filter goes here. http://google.com";

        // Check if there is a url in the text
        if (preg_match($reg_exUrl, $text, $url)) {

            // make the urls hyper links
            return preg_replace($reg_exUrl, "<a target='_blank' href='" . $url[0] . "'>" . $url[0] . "</a> ", $text);
        } else {

            // if no urls in the text just return the text
            return $text;
        }
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

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
                'views' => $views
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
                if ($checkout != null) {
                    foreach ($checkout as $key => $product) {
                        $append  = [
                            'qty'=> $product['qty'],
                            'id' => $product['item']->id,
                            'name' => $product['item']->name,
                        ];
                        array_push($data, $append);
                    }
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
        $PUSH_NOTIFICATION_RECEIVERS = [];
        $PUSH_NOTIFICATION = [];
        array_push($PUSH_NOTIFICATION_RECEIVERS, $seller_id);
        array_push($PUSH_NOTIFICATION, $notification);

        broadcast(new NewNotification($PUSH_NOTIFICATION_RECEIVERS, $PUSH_NOTIFICATION));
        return new ShopResource($data);
   
    }

    public function searchUsers($query)
    {
        $user_id = auth('sanctum')->user()->id;
        if(is_string($query)){
            $users = User::select('id', 'name', 'image')
            ->where('name', 'LIKE', "%{$query}%")
            ->get();
            foreach ($users as $key => $account) {
                $account->follows_you = count(FollowingTable::where(['sender_id' => $account->id, 'receiver_id' => $user_id])->get()) > 0 ? true : false;
                if ($user_id != $account->id) {
                    $account->is_following = count(FollowingTable::where(['receiver_id' => $account->id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
                }
            }
            return new ShopResource($this->paginate($users));
        }
    }
    public function searchProducts($query)
    {
        $user_id = auth('sanctum')->user()->id;
        if (is_string($query)) {
            $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
            foreach ($products as $key => $product) {
                $user = User::select('name', 'image')->where('id', $product->user_id)->first();
                $product->user = $user;
                $product->price = number_format($product->price, 0, '.', ',');
                $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id' => $product->id])->get()) > 0 ? true : false;    
            }
            return new ShopResource($this->paginate($products));
        }
    }
    public function searchBroadcasts($query)
    {
        $user_id = auth('sanctum')->user()->id;
        if (is_string($query)) {
            $posts = Post::where('body', 'LIKE', "%{$query}%")
            ->get();
            foreach ($posts as $key => $post) {
                $user = User::select('name', 'image')->where('id', $post->user_id)->first();
                $post->user = $user;
                $post->likes = count(PostsLike::where("post_id", $post->id)->get());
                $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

                $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
                $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

                $post->comments = count(Post::where('post_id', $post->id)->get());

                $post->timeago = $this->updateTime($post->created_at);
                $post->info_display = 'retweets';
                $post->body = $this->checkForLinks($post->body);
                $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

                $post->type = 'broadcast';
                $post->is_comment = $post->post_id != null ? true : false;
                if ($post->is_comment) {
                    $post->type = 'comment';
                    $original_post = Post::find($post->post_id);
                    if ($original_post->user_id == $post->user_id) {
                        $post->is_thread = true;
                        $post->comment_is_thread = true;
                        $post->type = "thread";
                    } else {
                        $user = User::select('name', 'image')->where('id', $original_post->user_id)->first();
                        $original_post->user = $user;
                        $original_post->likes = count(PostsLike::where("post_id", $original_post->id)->get());
                        $original_post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;

                        $original_post->rebroadcasts = count(PostsRetweet::where("post_id", $original_post->id)->get());
                        $original_post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;

                        $original_post->comments = count(Post::where('post_id', $original_post->id)->get());

                        $original_post->timeago = $this->updateTime($original_post->created_at);
                        $original_post->info_display = 'retweets';
                        $original_post->body = $this->checkForLinks($original_post->body);
                        $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;

                        $original_post->type = 'broadcast';
                        $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                        $original_post->is_comment = $original_post->post_id != null ? true : false;
                        if ($original_post->is_comment) {
                            $original_post->type = 'comment';
                            $original_post = Post::find($original_post->post_id);
                            if ($original_post->user_id == $post->user_id) {
                                $original_post->is_thread = true;
                                $post->comment_is_thread = true;
                                $original_post->type = "thread";
                            }
                        } else {
                            if ($original_post->post_id == null && count(Post::where(['post_id' => $original_post->id, 'user_id' => $original_post->user_id])->get()) > 0) {
                                $original_post->is_thread = true;
                                $original_post->type = "thread";
                            }
                        }
                        $post->original_post = $original_post;
                    }
                } else {
                    if ($post->post_id == null && count(Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get()) > 0) {
                        $post->is_thread = true;
                        $post->type = "thread";
                    }
                }    
            }
            return new ShopResource($this->paginate($posts));
        }
    }

    public function sendFeedback(Request $request)
    {
        $this->validate($request, [
            'email'=> ["email", "required", "string"],
            'message'=> ["required", "string"],
            'name' => ["required", "string"]
        ]);
        $feedback = [
            'name'=> $request->input('name'),
            'email'=>$request->input('email'),
            'message'=>$request->input('message')
        ];
        $data = [
            'message'=> 'Thank you for taking your time to make us serve you better.'
        ];
        Mail::to('support@fuoye360.com')->send(new SendFeedbackMail($feedback));
        return response(new ShopResource($data));
    }
}
