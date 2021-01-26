<?php

use App\User;
use App\Product;
use App\ProductsView;
use App\FollowingTable;
use App\ProductsLifetime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->following = count(FollowingTable::where('sender_id', $user->id)->get());
    $user->followers = count(FollowingTable::where('receiver_id', $user->id)->get());
    $user->total_views = 0;
    $getUserProduct = Product::where('user_id', $user->id)->get();
    $user->products_lifetime = count($getUserProduct);
    
    if (count($getUserProduct) > 0) {
        foreach ($getUserProduct as $key => $product) {
            $user->total_views += count(ProductsView::where('product_id', $product->id)->get());
        }
    }
            
    return $user;
});

Route::middleware('auth:sanctum')->get('/authenticated', function () {
    return true;
});

Auth::routes();
Route::post('/dashboard/edit-profile', 'DashboardController@edit');
Route::get('/shop/wishlist', 'ShopController@wishlist');
Route::get('/shop/trending', 'ShopController@getTrending');
Route::get('/shop/inventory', 'ShopController@inventory');
Route::get('/shop/buy', 'ShopController@buy');
Route::get('/shop/cart', 'ShopController@cart');
Route::get('/shop/buy/{category}', 'ShopController@category');

Route::resource('shop', 'ShopController');
Route::get('/broadcasts', 'NewBroadcastController@index');

Route::get('/broadcast/bookmarks', 'BroadcastController@bookmarks');
Route::get('/broadcast/trending', 'BroadcastController@trending');
Route::get('/broadcast/thread/{id}', 'BroadcastController@thread');
Route::post('/broadcast/{id}/like', 'BroadcastController@like');
Route::post('/broadcast/{id}/unlike', 'BroadcastController@unlike');
Route::post('/broadcast/{id}/undorebroadcast', 'BroadcastController@undoRebroadcast');
Route::post('/broadcast/{id}/rebroadcast', 'BroadcastController@rebroadcast');
Route::post('/broadcast/{id}/add-bookmark', 'BroadcastController@addToBookmark');
Route::post('/broadcast/{id}/remove-bookmark', 'BroadcastController@removeFromBookmark');
Route::post('/broadcast/comment', 'BroadcastController@newComment');
Route::get('/broadcast/{id}/comments', 'BroadcastController@comments');
Route::resource('broadcast', 'BroadcastController');

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::get('account/{name}', 'AccountController@index');
Route::get('account/{id}/following', 'AccountController@isFollowing');
Route::get('account/{id}/follower', 'AccountController@hasFollower');
Route::get('notifications', 'AccountController@notifications');
Route::post('/{id}/delete-notification', 'AccountController@deleteNotification');

Route::post('account/{id}/follow', 'AccountController@follow');
Route::post('account/{id}/unfollow', 'AccountController@unfollow');
Route::post('account/update', 'AccountController@store');


Route::post('action/{product_id}/add-to-wishlist', 'ActionController@addToWishlist');
Route::post('action/{product_id}/update-views', 'ActionController@updateViews');
Route::post('action/{product_id}/remove-from-wishlist', 'ActionController@removeFromWishlist');
Route::post('action/{product_id}/add-to-cart/{qty}', 'ActionController@addToCart');
Route::post('action/{seller_id}/remove-seller-from-cart', 'ActionController@removeSellerFromCart');
Route::post('action/{seller_id}/contact-seller', 'ActionController@contactSeller');
Route::post('action/{product_id}/remove-product-from-cart/{seller_id}', 'ActionController@removeProductFromCart');
Route::post('action/{product_id}/product-impression/{type}', 'ActionController@productImpressions');
