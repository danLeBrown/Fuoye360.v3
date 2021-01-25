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


class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $get_products = [];
        $followings = FollowingTable::where(['sender_id' => $user_id])->get();
        foreach ($followings as $key => $following) {
            $following_products = Product::where('user_id', $following->receiver_id)->get();
            $user = User::select('name', 'image')->where('id', $following->receiver_id)->first();
            foreach ($following_products as $key => $product) {
                $product->user = $user;
                $product->price = number_format($product->price, 0, '.', ',');
                $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $product->id])->get()) > 0 ? true: false;
                $get_products[$product->id] = $product;
            }   
        }
        krsort($get_products);

        $products = $this->paginate($get_products);
        return  ShopResource::collection($products);
    }

    public function getTrending()
    {
        $trendings = DB::select("SELECT id, name, image, bio, geo_location FROM users WHERE geo_location != ''");
        $user_id = auth('sanctum')->user()->id;
        $newTrendings = array();

        foreach ($trendings as $key => $trending) {
            $getUserProduct = Product::where('user_id', $trending->id)->get();
            $trending->following = count(FollowingTable::where('sender_id', $trending->id)->get());
            $trending->followers = count(FollowingTable::where('receiver_id', $trending->id)->get());
            $trending->is_following = count(FollowingTable::where(['receiver_id'=> $trending->id, 'sender_id' => $user_id])->get()) >0 ? true: false;
            $trending->total_views = 0;
            if (count($getUserProduct) > 0) {
                foreach ($getUserProduct as $key => $product) {
                    $trending->total_views += count(ProductsView::where('product_id', $product->id)->get());
                }
                $newTrendings[$trending->total_views] = $trending;
            }
        }
        foreach ($newTrendings as $key => $user) {
            if($user->total_views < 5){
                unset($newTrendings[$key]);
            }
        }
        krsort($newTrendings); 

        return new ShopResource($newTrendings);
    }
    public function wishlist()
    {
        $user_id = auth('sanctum')->user()->id;
        $get_products = [];
        $wishlists = Wishlist::where('user_id' , $user_id)->get();
        foreach ($wishlists as $key => $wishlist) {
            $product = Product::find($wishlist->product_id);
            $user = User::select('name', 'image')->where('id', $product->user_id)->first();
            $product->user = $user;
            $product->price = number_format($product->price, 0, '.', ',');
            $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $product->id])->get()) > 0 ? true: false;
            
            $get_products[$wishlist->id] = $product;
            // array_push($get_products , $product);
        }
        \krsort($get_products);

        $products = $this->paginate($get_products);
        return ShopResource::collection($products);
    }

    public function buy()
    {
        $user_id = auth('sanctum')->user()->id;
        $products = Product::orderBy('id', 'desc')->paginate(15);
        foreach ($products as $key => $product) {
            $user = User::select('name', 'image')->where('id', $product->user_id)->first();
            $product->user = $user;
            $product->price = number_format($product->price, 0, '.', ',');
            $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $product->id])->get()) > 0 ? true: false;            
        }
        return ShopResource::collection($products);

    }

    public function inventory(){
        $user_id = auth('sanctum')->user()->id;
        $user = User::select('name', 'image')->where('id', $user_id)->first();

        $products = Product::where('user_id', $user_id)->orderBy('id', 'DESC')->paginate(15);
        foreach ($products as $key => $product) {
            $product->user = $user;
            $product->price = number_format($product->price, 0, '.', ',');
        }
        $data = array(
            'status' => 200,
            'products' => $products,
        );
        // return new ShopResource($data);
        return ShopResource::collection($products);
    }

    public function cart()
    {
        $user_id = auth('sanctum')->user()->id;
        $check_if_cart_exists = Session::has('shoppingCart')? true : false; 
        $status = 500;
        $session_cart = [];
        if($check_if_cart_exists){
            $status = 200;
            $oldCart = Session::has('shoppingCart') ? Session::get('shoppingCart') : null;
            $session_cart = new Cart($oldCart);
            $session_cart->filterCart();
            if (is_numeric($session_cart->totalPrice)) {
                $session_cart->totalPrice = number_format($session_cart->totalPrice, 0, '.', ',');
            }

            $shopping_cart = $session_cart->filter;
            foreach ($shopping_cart as $key => $cart) {
                $items = $cart['products'];
                foreach ($items as $key => $item) {
                    if (is_numeric($item['item']->price)) {
                        $item['item']->price = number_format($item['item']->price, 0, '.', ',');
                    }
                    $item['item']->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $item['item']->id])->get()) > 0 ? true: false;
                }

            }

            $data = array(
                'shopping_cart' => $session_cart,
                'status' => $status
            );
        }else{
            $data = array(
                'shopping_cart' => $session_cart,
                'status' => $status
            );
        }
        return new ShopResource($data);
    }

    public function category($category)
    {
        $productArray = ['arts', 'computing', 'education', 'ecomponents', 'electronics', 'fashion', 'food', 'furniture', 'gifts', 'health', 'housing', 'music', 'phones', 'services', 'stationaries', 'sports', 'transport', 'uniforms', 'others'];

        if (in_array($category, $productArray)) {
            $status = 200;
            $user_id = auth('sanctum')->user()->id;
            $products = Product::where('category', $category)->orderBy('id', 'desc')->paginate(15);
            foreach ($products as $key => $product) {
                $user = User::select('name', 'image')->where('id', $product->user_id)->first();
                $product->user = $user;
                $product->price = number_format($product->price, 0, '.', ',');
                $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $product->id])->get()) > 0 ? true: false;
                
            }
        }else{
            $status = 500;
            $products = [];
        }

        $data = array(
            'status' => $status,
            'products' => $products
        );

        // return  new ShopResource($data);
        return ShopResource::collection($products);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $request->validate([
            'description' => 'required',
            'price' => 'required',
            'status' => 'required',
            'category' => 'required',
            'name' => 'required',
            'image' => 'required',
        ]);
        $product = new Product();
        $product->name = $request->input('name');
        $product->user_id = $user_id;
        $product->status = $request->input('status');
        $product->category = $request->input('category');
        $product->price = $request->input('price');
        $product->tags = $request->input('tags');
        $product->description = $request->input('description');
        if ($request->hasFile('image')) {
            // Get File name and extension
            $FileNameWithExt = $request->file('image')->getClientOriginalName();
            // Get File name
            $fileName = pathinfo($FileNameWithExt, PATHINFO_FILENAME);
            // Get File ext
            $fileExt = $request->file('image')->getClientOriginalExtension();
            // File name to store
            $fileNameToStore = time().'.'.$fileExt;
            // Store Image
            $path = $request->file('image')->storeAs('public/product_images', $fileNameToStore);
            # code...
        }
        $product->image = $fileNameToStore;
        $product->save();
        $product->price = number_format($product->price, 0, '.', ',');
        $product->user = User::select('name', 'image')->where('id', $user_id)->first();
        $followers = FollowingTable::where('receiver_id', $user_id)->get();
        if (count($followers) > 0) {
            foreach ($followers as $key => $follower) {
                $notification = new Notification();
                $notification->receiver_id = $follower->sender_id;
                $notification->sender_id = $user_id;
                $notification->type = 'new-product';
                $notification->data = \json_encode([$product->id]);
                $notification->save();
            }
        }
        $data = [
            'status' => 200,
            'product' => $product,
        ];

        return new ShopResource($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $products = Product::where('user_id', $id)->orderBy('id', 'desc')->paginate(15);
        foreach ($products as $key => $product) {
            $user = User::select('name', 'image')->where('id', $product->user_id)->first();
            $product->user = $user;
            $product->price = number_format($product->price, 0, '.', ',');
            $product->in_wishlist = count(Wishlist::where(['user_id' => $user_id, 'product_id'=> $product->id])->get()) > 0 ? true: false;
        }

        return ShopResource::collection($products);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
