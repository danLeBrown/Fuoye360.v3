<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Product;
use App\Wishlist;
use App\Notification;
use App\FollowingTable;
use App\ProductsView;
use App\ProductsLifetime;
use App\Traits\ResourceTrait;

class ProductController extends Controller
{
    use ResourceTrait;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $products = [];
        $followings = FollowingTable::where(['sender_id' => $user_id])->get();
        foreach ($followings as $key => $following) {
            $following_products = Product::where('user_id', $following->receiver_id)->get();
            $user = User::select('name', 'image')->where('id', $following->receiver_id)->first();
            foreach ($following_products as $key => $product) {
                $product->createProductData($product);
                $products[$product->id] = $product;
            }   
        }
        krsort($products);
        return $this->createResource($products);
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

        return new DataResource($newTrendings);
    }
    public function wishlist()
    {
        $user_id = auth('sanctum')->user()->id;
        $products = [];
        $wishlists = Wishlist::where('user_id' , $user_id)->get();
        foreach ($wishlists as $key => $wishlist) {
            $product = Product::find($wishlist->product_id);
            $product->createProductData($product);
            $products[$wishlist->id] = $product;
        }
        \krsort($products);

        return $this->createResource($products);
    }

    public function buy()
    {
        $products = Product::orderBy('id', 'desc')->get();
        foreach ($products as $key => $product) {
            $product->createProductData($product);
        }
        return $this->createResource($products);
    }

    public function inventory(){
        $user_id = auth('sanctum')->user()->id;
        $user = User::select('name', 'image')->where('id', $user_id)->first();
        $products = Product::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
        foreach ($products as $key => $product) {
            $product->user = $user;
            $product->price = number_format($product->price, 0, '.', ',');
        }
        return $this->createResource($products);
    }

    public function category($category)
    {
        $productArray = ['arts', 'computing', 'education', 'ecomponents', 'electronics', 'fashion', 'food', 'furniture', 'gifts', 'health', 'housing', 'music', 'phones', 'services', 'stationaries', 'sports', 'transport', 'uniforms', 'others'];

        if (in_array($category, $productArray)) {
            $status = 200;
            $user_id = auth('sanctum')->user()->id;
            $products = Product::where('category', $category)->orderBy('id', 'desc')->get();
            foreach ($products as $key => $product) {
                $product->createProductData($product);                
            }
        }else{
            $products = [];
        }
        return $this->createResource($products);
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
            'name' => 'required',
            'image' => 'required',
            'price' => 'required',
            'status' => 'required',
            'category' => 'required',
            'description' => 'required',
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
        $add_to_productslifetime = ProductsLifetime::where('user_id', $user_id)->get();
        if(count($add_to_productslifetime) > 0){
            $add_to_productslifetime->count += 1; 
            $add_to_productslifetime->save();
        }else{
            $add_to_productslifetime = new ProductsLifetime();
            $add_to_productslifetime->user_id = $user_id;
            $add_to_productslifetime->count = 1;
            $add_to_productslifetime->save();
        }
        $data = [
            'status' => 200,
            'product' => $product,
            'products_lifetime' => $add_to_productslifetime
        ];
        return new DataResource($data);
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
        $products = Product::where('user_id', $id)->orderBy('id', 'desc')->get();
        foreach ($products as $key => $product) {
            $product->createProductData($product);
        }
        return $this->createResource($products);
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
