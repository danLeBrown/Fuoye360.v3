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
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Image;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
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

        if ($seconds <= 60){

        return "1s";

        } else if ($minutes <= 60){

            if ($minutes == 1){

                return "1m";

            } else {

                return $minutes."m";

            }

        } else if ($hours <= 24){

            if ($hours == 1){

                return "1hr";

            } else {

                return $hours."hrs";

            }

        } else if ($days <= 7){

            if ($days == 1){

                return "1d";

            } else {

                return $days."d";

            }

        }
        //  else if ($weeks <= 4.3){

        //     if ($weeks == 1){

        //         return "a week ago";

        //     } else {

        //         return "$weeks weeks ago";

        //     }

        // } else if ($months <= 12){

        //     if ($months == 1){

        //         return "a month ago";

        //     } else {

        //         return "$months months ago";

        //     }

        // } else {

        //     if ($years == 1){

        //         return "1 year ago";

        //     } else {

        //         return "$years years ago";

        //     }
        else{
            // return getdate($time_ago);
            return getdate($time_ago)['mday']." ".substr(getdate($time_ago)['month'], 0, 3)." '".substr(getdate($time_ago)['year'], 2, 4);
        }
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
    public function index($name)
    {
        $user_id = auth('sanctum')->user()->id;
        $visited = User::select('id','image', 'name', 'bio', 'geo_location')->where('name', $name)->first();
        $visited->following = count(FollowingTable::where('sender_id', $visited->id)->get());
        $visited->followers = count(FollowingTable::where('receiver_id', $visited->id)->get());
        if ($user_id != $visited->id) {
            $visited->is_following = count(FollowingTable::where(['receiver_id'=> $visited->id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
        }
        $data = ['visited' => $visited];
        return new ShopResource($data);
    }

    public function follow($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $receiver_id = $id;
        $checkFollower = FollowingTable::where(['sender_id'=> $user_id, 'receiver_id' => $receiver_id])->get();
        if (count($checkFollower) > 0 || $user_id == $receiver_id ) {
            $status = 500;
        }else{
            $newFollower = new FollowingTable;
            $newFollower->sender_id =  $user_id;
            $newFollower->receiver_id =  $receiver_id;
            $newFollower->save();
        
            $receiver_followers = count(FollowingTable::where('receiver_id', $receiver_id)->get());
            $sender_following = count(FollowingTable::where('sender_id', $user_id)->get());

            
            $notification = new Notification;
            $notification->receiver_id = $receiver_id;
            $notification->sender_id = $user_id;
            $notification->type = "follow";
            $notification->data = \json_encode( array('sender_id' => $user_id));
            $notification->save();
            
            $receiver = User::find($receiver_id);
            $status = 200;
        }
        $data = array(
            'status' => $status,
            'receiver_count' => count(FollowingTable::where('receiver_id', $receiver_id)->get()),
            'sender_count' => count(FollowingTable::where('sender_id', $user_id)->get()),
        );
        return new ShopResource($data);
    }


    public function unfollow($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $receiver_id = $id;
        $checkFollower = FollowingTable::where(['sender_id'=> $user_id, 'receiver_id' => $receiver_id])->get();
            if (count($checkFollower) <= 0 || $user_id == $receiver_id) {
                $status = 500;
            }else{
                $checkFollower[0]->delete();
                $receiver = User::find($receiver_id);
                $status = 200;
            }
        $data = array(
            'status' => $status,
            'receiver_count' => count(FollowingTable::where('receiver_id', $receiver_id)->get()),
            'sender_count' => count(FollowingTable::where('sender_id', $user_id)->get()),
        );
        return new ShopResource($data);
    }

    public function isFollowing($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $accounts = FollowingTable::where('sender_id', $id)->orderBy('created_at', 'desc')->paginate(15);
        foreach ($accounts as $key => $account) {
            $following = User::find($account->receiver_id);
            $account->name = $following->name;
            $account->image = $following->image;
            $account->id = $account->receiver_id;
            $account->follows_you = count(FollowingTable::where(['sender_id'=> $account->receiver_id, 'receiver_id'=> $user_id])->get()) > 0 ? true : false;
            if($user_id != $account->receiver_id){
                $account->is_following = count(FollowingTable::where(['receiver_id'=> $account->receiver_id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
            }
        }

        // $accounts = DB::select("SELECT following_tables.*, users.id, users.name, users.image FROM following_tables, users WHERE sender_id = $id AND receiver_id = users.id ORDER BY following_tables.created_at DESC LIMIT 15");


        // $data = array(
        //     'status' => 200,
        //     'accounts' => $accounts
        // );

        return ShopResource::collection($accounts);

        // return new ShopResource($data);
    }

    public function hasFollower($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $accounts = FollowingTable::where('receiver_id', $id)->orderBy('created_at', 'desc')->paginate(15);
        foreach ($accounts as $key => $account) {
            $following = User::find($account->sender_id);
            $account->name = $following->name;
            $account->image = $following->image;
            $account->id = $account->sender_id;
            $account->follows_you = count(FollowingTable::where(['sender_id'=> $account->sender_id, 'receiver_id'=> $user_id])->get()) > 0 ? true : false;
            if($user_id != $account->sender_id){
                $account->is_following = count(FollowingTable::where(['receiver_id'=> $account->sender_id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
            }
        }

        // $accounts = DB::select("SELECT following_tables.*, users.id, users.name, users.image FROM following_tables, users WHERE sender_id = $id AND receiver_id = users.id ORDER BY following_tables.created_at DESC LIMIT 15");


        // $data = array(
        //     'status' => 200,
        //     'accounts' => $accounts
        // );

        return ShopResource::collection($accounts);

        // return new ShopResource($data);
    }
    
    public function notifications()
    {
        $user_id = auth('sanctum')->user()->id;
        $notifications = Notification::where('receiver_id', $user_id)->orderBy('created_at', 'desc')->paginate(15);

        foreach ($notifications as $key => $notification) {
            $user = User::select('name', 'image')->where('id', $notification->sender_id)->first();
            $notification->sender = $user;
            $notification->relative_at = $this->updateTime($notification->created_at);
        }

        // $data = array(
        //     'status' => 200,
        //     'notifications' => $notifications
        // );
        // return new ShopResource($data);
        return ShopResource::collection($notifications);

    }

    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->delete();
        }
        $data = array(
            'status' => 200,
        );
        return new ShopResource($data);
        
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
        $user = User::find($user_id);
        $this->validate($request, [
            'location' => ["required", "string"],
            'telephone' => ["required", "string"],
            'image' => ["nullable"],
            'bio' => ["nullable"],
        ]);
        $inputStatus = 0;
        if ($request->hasFile('image')) {
            // Get File name and extension
            $FileNameWithExt = $request->file('image')->getClientOriginalName();
            // Get File name
            // $fileName = pathinfo($FileNameWithExt, PATHINFO_FILENAME);
            // Get File ext
            $fileExt = $request->file('image')->getClientOriginalExtension();
            // Store Image
            $fileNameToStore = time().'.'.$fileExt;
            
            // File name to store
            $path = $request->file('image')->storeAs('public/profile_images', $fileNameToStore);
            list($width, $height) = getimagesize(storage_path('app/public/profile_images/'.$fileNameToStore));
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
            $img = Image::make(storage_path('app/public/profile_images/'.$fileNameToStore))->resize($newwidth, $newheight)->save( storage_path('app/public/profile_images/'.$fileNameToStore));
            if ($user->image != 'default.jpg') {
                Storage::delete('public/profile_images/'.$user->image);
            }
            $user->image = $fileNameToStore;
            $inputStatus += 1;
        }
        if (is_string($request->input('location')) && $request->input('location') != '' && is_numeric($request->input('telephone'))) {
            $user->geo_location = htmlspecialchars($request->input('location'));
            $user->telephone = htmlspecialchars($request->input('telephone'));
            if(is_string($request->input('bio')) && $request->input('bio') != ''){
                $user->bio = htmlspecialchars($request->input('bio'));
            }
            $inputStatus += 1;
        }
        if ($inputStatus == 1 || $inputStatus == 2) {
            $user->save();
            $status = 200;
        }
        $data = array(
            'status' => $status,
        );

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
        //
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
