<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Product;
use App\FollowingTable;
use Illuminate\Http\Request;
use App\Traits\ResourceTrait;
use App\Http\Resources\DataResource;

class SearchController extends Controller
{
    use ResourceTrait;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function search(Request $request)
    {
        if ($request->has('filter_by')) {
            $filter_by = $request->input('filter_by');
            
            if($filter_by == 'users'){
                return $this->searchUsers($request->input('q'));
            }
            if ($filter_by == 'products') {
                return $this->searchProducts($request->input('q'));
            }
            if ($filter_by == 'broadcasts') {
                return $this->searchBroadcasts($request->input('q'));
            }
        }
    }

    public function findUser($name)
    {
        $visited = User::getUserDataFromName($name);
        return new DataResource($visited);
    }
    
    public function searchUsers($query)
    {
        if (is_string($query)) {
            $users = User::select('id', 'name', 'image')
            ->where('name', 'LIKE', "%{$query}%")
            ->get();
            foreach ($users as $key => $account) {
                $account->createUserData($account);
            }
            return $this->createResource($users);
        }

    }

    public function searchProducts($query)
    {
        if (is_string($query)) {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();
            foreach ($products as $key => $product) {
                $product->createProductData($product);
            }

            return $this->createResource($products);
        }
    }

    public function searchBroadcasts($query)
    {
        if (is_string($query)) {
            $posts = Post::where('body', 'LIKE', "%{$query}%")
                ->get();
            foreach ($posts as $key => $post) {
                $post->createPostData($post);
            }
            return $this->createResource($posts);
        }
    }

    public function getUserFollowers()
    {
        # code...
    }

}
