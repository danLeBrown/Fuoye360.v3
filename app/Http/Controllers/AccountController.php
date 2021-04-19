<?php

namespace App\Http\Controllers;

use App\User;
use App\Notification;
use App\FollowingTable;
use App\Traits\TimeagoTrait;
use Illuminate\Http\Request;
use App\Traits\ResourceTrait;
use App\Events\NewNotification;
use App\Http\Resources\DataResource;
use App\Http\Resources\ShopResource;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    use ResourceTrait, TimeagoTrait;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($name)
    {
        $visited = User::getUserDataFromName($name);
        return new DataResource($visited);
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
        $PUSH_NOTIFICATION_RECEIVERS = [];
        $PUSH_NOTIFICATION = [];
        array_push($PUSH_NOTIFICATION, $notification);
        array_push($PUSH_NOTIFICATION_RECEIVERS, $receiver_id);
        broadcast(new NewNotification($PUSH_NOTIFICATION_RECEIVERS, $PUSH_NOTIFICATION));
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
        $accounts = FollowingTable::where('sender_id', $id)->orderBy('created_at', 'desc')->get();
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
        return $this->createResource($accounts);
    }

    public function hasFollower($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $accounts = FollowingTable::where('receiver_id', $id)->orderBy('created_at', 'desc')->get();
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
        return $this->createResource($accounts);
    }
    
    public function notifications()
    {
        $user_id = auth('sanctum')->user()->id;
        $notifications = Notification::where('receiver_id', $user_id)->orderBy('created_at', 'desc')->get();

        foreach ($notifications as $key => $notification) {
            $user = User::select('name', 'image')->where('id', $notification->sender_id)->first();
            $notification->sender = $user;
            $notification->data = json_decode($notification->data, true);
            $notification->relative_at = $this->timeago($notification->created_at);
        }
        return $this->createResource($notifications);
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
