<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Post;
use App\User;
use DateTime;
use App\PostsLike;
use App\Notification;
use App\PostsRetweet;
use App\PostsBookmark;
use App\FollowingTable;
use App\Events\NewBroadcast;
use Illuminate\Http\Request;
use App\Events\NewNotification;
use Illuminate\Support\Collection;
use App\Http\Resources\ShopResource;
use Illuminate\Pagination\Paginator;
use Intervention\Image\Facades\Image;
use Illuminate\Pagination\LengthAwarePaginator;

class BroadcastController extends Controller

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
            */
        else{
            // return getdate($time_ago);
            return getdate($time_ago)['mday']." ".substr(getdate($time_ago)['month'], 0, 3)." '".substr(getdate($time_ago)['year'], 2, 4);
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
        if(preg_match($reg_exUrl, $text, $url)) {

            // make the urls hyper links
            return preg_replace($reg_exUrl, "<a target='_blank' href='".$url[0]."'>".$url[0]."</a> ", $text);
        } else {

            // if no urls in the text just return the text
            return $text;

        }
    }
    public function index()
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::find($user_id);
        $broadcasts = [];
        $followings = FollowingTable::where('sender_id', $user_id)->get();

        foreach ($followings as $key => $following) {
            $posts = Post::where('user_id', $following->receiver_id)->get();
            foreach ($posts as $key => $post) {
                $post->orderby_date = $post->created_at;
                $post->is_following = true;
                array_push($broadcasts, $post);
            }
            $retweets = PostsRetweet::where('user_id', $following->receiver_id)->get();
            foreach ($retweets as $key => $retweet) {
                $post = Post::where('id', $retweet->post_id)->first();
                $post->orderby_date = $retweet->created_at;
                $retweeter = User::select('id', 'name', 'image')->where('id', $following->receiver_id)->first();
                $post->retweeter = $retweeter;
                $post->is_following = true;
                array_push($broadcasts, $post);
            }
        }

        $userTweets = Post::where('user_id', $user_id)->get();
        foreach ($userTweets as $key => $post) {
            $post->orderby_date = $post->created_at;
            $retweets = count(PostsRetweet::where(['user_id' => $user_id, 'post_id' => $post->id])->get());
            if($retweets > 0){
                $retweeter = User::select('id', 'name', 'image')->where('id', $user_id)->first();
                $post->retweeter = $retweeter;                
            }
            array_push($broadcasts, $post);
        }

        $newPosts = array();
        $post_counter = array();
        foreach ($broadcasts as $key => $post) {
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
            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
    
            $post->type = 'broadcast';        
            $post->is_comment = $post->post_id != null ? true : false; 
            if($post->is_comment){
                $post->type = 'comment';
                $original_post = Post::find($post->post_id);
                if($original_post->user_id == $post->user_id){
                    $post->is_thread = true;
                    $post->comment_is_thread = true;
                    $post->type = "thread";
                }else{
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
                    $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0? true : false;

                    $original_post->type = 'broadcast';        
                    $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                    $original_post->is_comment = $original_post->post_id != null ? true : false; 
                    if($original_post->is_comment){
                        $original_post->type = 'comment';
                        $original_post = Post::find($original_post->post_id);
                        if($original_post->user_id == $post->user_id){
                            $original_post->is_thread = true;
                            $post->comment_is_thread = true;
                            $original_post->type = "thread";
                        }
                    }else{
                        if($original_post->post_id == null && count(Post::where(['post_id' => $original_post->id, 'user_id' => $original_post->user_id])->get()) > 0){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }
                    }
                    $post->original_post = $original_post;
                    
                }
            }else{
                if($post->post_id == null && count(Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get()) > 0){
                    $post->is_thread = true;
                    $post->type = "thread";
                }
            }        

            if (array_key_exists($post->id, $post_counter)) {
                if ($post_counter[$post->id]['retweeter_id'] == null) {
                    if ($post_counter[$post->id]['user_id'] != $post->retweeter->id) {
                        $post_counter[$post->id]['retweets_count'] += 1;
                        $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                        $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                    }
                    else{
                        if($post_counter[$post->id]['retweets_count'] > 2){
                            $post_counter[$post->id]['retweets_count'] -= 1;
                        }else{
                            $post_counter[$post->id]['retweets_count'] = 1;
                        }
                        $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                        $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                    }
                }else{
                    if(isset($post->retweeter->id)){
                        if ($post_counter[$post->id]['retweeter_id'] != $post->retweeter->id) {
                            $post_counter[$post->id]['retweets_count'] += 1;
                            $post->retweets_count = $post_counter[$post->id]['retweets_count'];                            
                            $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                        }else{
                            $post_counter[$post->id]['retweets_count'] += 1;
                            $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                        }
                    }
                }
                unset($newPosts[$post_counter[$post->id]['time']]);
                $post_counter[$post->id]['time'] = strtotime($post->orderby_date);

                $newPosts[strtotime($post->orderby_date)] = $post;
            }else{
                $post_counter[$post->id] = array(
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'time' => strtotime($post->orderby_date),
                    'retweeter_id' => isset($post->retweeter->id) ? $post->retweeter->id : null,
                    'retweets_count' => isset($post->retweeter->id) ? 1 : 0,
                );
                $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                $newPosts[strtotime($post->orderby_date)] = $post;
            } 
        }
        krsort($newPosts);

        foreach ($newPosts as $key => $value) {
            if($value->comment_is_thread){
                unset($newPosts[$key]);
            }
        }
        // $data = array(
        //     'status' => 200,
        //     'broadcasts' => $newPosts,
        // );
        return  ShopResource::collection($this->paginate($newPosts));
    }

    public function bookmarks()
    {
        $user_id = auth('sanctum')->user()->id;

        
        $getBookmarks = PostsBookmark::where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        $bookmarks = [];
        foreach ($getBookmarks as $key => $bookmark) {

            $post = Post::find($bookmark->post_id);
            $user = User::select('name', 'image')->where('id', $post->user_id)->first();
            $post->user = $user;
            $post->timeago = $this->updateTime($post->created_at);
            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            $post->comments = count(Post::where('post_id', $post->id)->get());

            $post->body = $this->checkForLinks($post->body);
            $post->bookmarked = true;
            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;

            $post->type = 'broadcast';        
            $post->is_comment = $post->post_id != null ? true : false; 
            if($post->is_comment){
                $post->type = 'comment';
                $original_post = Post::find($post->post_id);
                if($original_post->user_id == $post->user_id){
                    $post->is_thread = true;
                    $post->type = "thread";
                }else{
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
                    $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0? true : false;

                    $original_post->type = 'broadcast';        
                    $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                    $original_post->is_comment = $original_post->post_id != null ? true : false; 
                    if($original_post->is_comment){
                        $original_post->type = 'comment';
                        $original_post = Post::find($original_post->post_id);
                        if($original_post->user_id == $original_post->user_id){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }
                    }
                    $post->original_post = $original_post;
                    
                }
            }
            array_push($bookmarks, $post);
        }
        // $data = array(
        //     'status' => 200,
        //     'broadcasts' => $bookmarks,
        //     'postCounter'=> $post_counter,
        // );
        // return new ShopResource($data);

        return  ShopResource::collection($this->paginate($bookmarks));

    }

    public function trending()
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::find($user_id);

        $posts = Post::all();
        $date = date('Y/m/d h:i:s');
        $dt = new DateTime($date);
        $today = $dt->format('Y-m-d');
        $check_retweet_date = array();
        
        foreach ($posts as $key => $post) {
            $post->orderby_date = $post->created_at;
            $date_recived = $post->orderby_date;            
            $getDateOnly = new DateTime($date_recived);
            $day = $getDateOnly->format('Y-m-d');
            if($today != $day){
                unset($posts[$key]);
            }else{
                $user = User::select('id', 'name', 'image')->where('id', $post->user_id)->first();
                $post->user = $user;
                $post->total_retweets = count(PostsRetweet::where('post_id', $post->id)->get());
                $post->total_likes = count(PostsLike::where('post_id', $post->id)->get());
                $post->timeago = $this->updateTime($post->orderby_date);
                $post->retweets_count = 0;
                $post->likes_count = 0;
    
                if ($post->total_retweets < 5 && $post->total_likes < 5){
                    unset($posts[$key]);
                }else{
                    $checkFollowings = FollowingTable::where('sender_id', $user->id)->get();
                    if(count($checkFollowings) > 0){
                        $check_retweet_date[$post->id]['retweet_time'] = $checkFollowings[0]->created_at;
                        $check_retweet_date[$post->id]['like_time'] = $checkFollowings[0]->created_at;
                        foreach ($checkFollowings as $key => $checkFollowing){
                            $check_following_that_retweeted = PostsRetweet::where(['post_id'=> $post->id, 'user_id'=>$checkFollowing->receiver_id])->get();
                            if(count($check_following_that_retweeted) > 0){
                                $post->retweets_count += 1;
                                $retweeter = User::select('id', 'name', 'image')->where('id', $checkFollowing->receiver_id)->first();
                                $post->retweeter = $retweeter;

                                if($check_following_that_retweeted[0]->created_at > $check_retweet_date[$post->id]['retweet_time']){
                                    $check_retweet_date[$post->id]['retweet_time'] = $check_following_that_retweeted[0]->created_at;
                                    $retweeter = User::select('id', 'name', 'image')->where('id', $check_following_that_retweeted[0]->user_id)->first();
                                    $post->retweeter = $retweeter;
                                }
                            }
    
                        }
    
                        foreach ($checkFollowings as $key => $checkFollowing){
                            $check_following_that_liked = PostsLike::where(['post_id'=> $post->id, 'user_id'=>$checkFollowing->receiver_id])->get();
                            if(count($check_following_that_liked) > 0){
                                $post->likes_count += 1;
                                $retweeter = User::select('id', 'name', 'image')->where('id', $checkFollowing->receiver_id)->first();
                                $post->retweeter = $retweeter;
                                if($check_following_that_liked[0]->created_at > $check_retweet_date[$post->id]['like_time']){
                                    $check_retweet_date[$post->id]['like_time'] = $check_following_that_liked[0]->created_at;
                                    $retweeter = User::select('id', 'name', 'image')->where('id', $check_following_that_liked[0]->user_id)->first();
                                    $post->retweeter = $retweeter;                                
                                }
                            }
                        }
    
                        if($post->likes_count > $post->retweets_count){
                            $post->info_display = 'likes';
                        }else{
                            $post->info_display = 'retweets';
                        }
                    }
                }
            }
        }
        foreach ($posts as $key => $post) {
            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            
            $post->comments = count(Post::where("post_id", $post->id)->get());
            $post->body = $this->checkForLinks($post->body);
            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;

    
            $post->type = 'broadcast';        
            $post->is_comment = $post->post_id != null ? true : false; 
            if($post->is_comment){
                $post->type = 'comment';
                $original_post = Post::find($post->post_id);
                if($original_post->user_id == $post->user_id){
                    $post->is_thread = true;
                    $post->comment_is_thread = true;
                    $post->type = "thread";
                }else{
                    $user = User::select('name', 'image')->where('id', $original_post->user_id);
                    $original_post->user = $user;
                    $original_post->likes = count(PostsLike::where("post_id", $original_post->id)->get());
                    $original_post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;
    
                    $original_post->rebroadcasts = count(PostsRetweet::where("post_id", $original_post->id)->get());
                    $original_post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;
                    
                    $original_post->comments = count(Post::where('post_id', $original_post->id)->get());
                
                    $original_post->timeago = $this->updateTime($original_post->created_at);
                    $original_post->info_display = 'retweets';
                    $original_post->body = $this->checkForLinks($original_post->body);
                    $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0? true : false;

                    $original_post->type = 'broadcast';        
                    $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                    $original_post->is_comment = $original_post->post_id != null ? true : false; 
                    if($original_post->is_comment){
                        $original_post->type = 'comment';
                        $original_post = Post::find($original_post->post_id);
                        if($original_post->user_id == $post->user_id){
                            $original_post->is_thread = true;
                            $post->comment_is_thread = true;
                            $original_post->type = "thread";
                        }
                    }else{
                        if($original_post->post_id == null && count(Post::where(['post_id' => $original_post->id, 'user_id' => $original_post->user_id])->get()) > 0){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }
                    }
                    $post->original_post = $original_post;
                    
                }
            }else{
                if($post->post_id == null && count(Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get()) > 0){
                    $post->is_thread = true;
                    $post->type = "thread";
                }
            }        

        }
        // $data = array(
        //     'status' => 200,
        //     'broadcasts' => $posts,
        //     'postCounter'=> $post_counter,
        // );
        // return new ShopResource($data);
    
        return  ShopResource::collection($this->paginate($posts));

    }

    public function like($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id;
        $status = 500;
        $likes_count = PostsLike::where(['post_id'=> $post_id, 'user_id'=>$user_id])->get();
        $count = count($likes_count);
        if ($count <= 0) {
            if (is_numeric($post_id)) {
                $newLike = new PostsLike();
                $newLike->user_id = $user_id;
                $newLike->post_id = $id;
                $newLike->save();
                $status = 200;

                $blogger_id = Post::find($post_id)->user_id;
                if ($blogger_id != $user_id) {
                    $notification = new Notification;
                    $notification->receiver_id = $blogger_id;
                    $notification->sender_id = $user_id;
                    $notification->type = 'like-broadcast';
                    $notification->data_id = $post_id;
                    $notification->save();
                }

                $likes_count = count(PostsLike::where('post_id', $post_id)->get());
            }
        }
        $data = array(
            'status' => $status,
            'likes' => $likes_count, 
        );
        $PUSH_NOTIFICATION_RECEIVERS = [];
        array_push($PUSH_NOTIFICATION_RECEIVERS, $blogger_id);
        broadcast(new NewNotification($PUSH_NOTIFICATION_RECEIVERS, $notification->id));
        return new ShopResource($data);

    }

    public function unlike($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id;
        $status = 500;
        $likes_count = PostsLike::where(['post_id'=> $post_id, 'user_id'=>$user_id])->get();
        $count = count($likes_count);
        if ($count > 0) {
            if (is_numeric($post_id)) {
                $likes_count[0]->delete();
                $status = 200;
                $likes_count = count(PostsLike::where('post_id', $post_id)->get());
            }
        }
        $data = array(
            'status' => $status,
            'likes' => $likes_count, 
        );

        return new ShopResource($data);
    }

    public function rebroadcast($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id;
        $status = 500;
        $rebroadcasts = PostsRetweet::where(['post_id'=> $post_id, 'user_id'=>$user_id])->get();
        $count = count($rebroadcasts);
        if ($count <= 0) {
            if (is_numeric($post_id)) {
                $newRetweet = new PostsRetweet();
                $newRetweet->user_id = $user_id;
                $newRetweet->post_id = $post_id;
                $newRetweet->save();
                $status = 200;

                $blogger_id = Post::find($post_id)->user_id;
                if ($blogger_id != $user_id) {
                    $notification = new Notification;
                    $notification->receiver_id = $blogger_id;
                    $notification->sender_id = $user_id;
                    $notification->type = 'rebroadcast';
                    $notification->data_id = $post_id;
                    $notification->save();
                }
                $rebroadcasts = count(PostsRetweet::where('post_id', $post_id)->get());
            }
        }
        $data = array(
            'status' => $status,
            'rebroadcasts' => $rebroadcasts, 
        );
        $PUSH_NOTIFICATION_RECEIVERS = [];
        array_push($PUSH_NOTIFICATION_RECEIVERS, $blogger_id);
        broadcast(new NewNotification($PUSH_NOTIFICATION_RECEIVERS, $notification->id));
        return new ShopResource($data);
    }

    public function undoRebroadcast($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id; 
        $status = 500;
        $rebroadcasts = PostsRetweet::where(['user_id'=>$user_id, 'post_id'=>$post_id])->get();
        if (is_numeric($post_id)) {
            if (count($rebroadcasts) > 0) {
                $rebroadcasts[0]->delete();
            }
            $rebroadcasts = count(PostsRetweet::where('post_id', $post_id)->get());
            $status = 200;
        }
        $data = array(
            'status' => $status,
            'rebroadcasts' => $rebroadcasts, 
        );
        return new ShopResource($data);
    }

    public function addToBookmark($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id; 
        $status = 500;
        $checkBookmark = PostsBookmark::where(['user_id'=> $user_id, 'post_id' => $post_id])->get();
        if (count($checkBookmark) <= 0) {
            $newBookmark = new PostsBookmark ();
            $newBookmark->user_id = $user_id;
            $newBookmark->post_id = $post_id;
            $newBookmark->save();
            $status = 200;                
        }
        $data = array(
            'status' => $status,
        );
        return new ShopResource($data);
    }

    public function removeFromBookmark($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $post_id = $id; 
        $status = 500;
        $checkBookmark = PostsBookmark::where(['user_id'=> $user_id, 'post_id' => $post_id])->get();
        if (count($checkBookmark) > 0) {
            $checkBookmark[0]->delete();
            $status = 200;                
        }
        $data = array(
            'status' => $status,
        );
        return new ShopResource($data);
    
    }
    
    public function thread($id)
    {
        $status = 500;
        $post = null;
        $user_id = auth('sanctum')->user()->id;
        if(is_numeric($id)){
            $broadcasts = [];
            $post = Post::find($id);
            if(!$post){
                return json_encode(array('status'=> 500, 'messagge'=> 'Post Deleted'));
            }
            $status = 200;
            $user = User::select('name', 'image')->where('id', $post->user_id)->first();
            $post->user = $user;
            $post->timeago = $this->updateTime($post->created_at);
            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            
            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            
            $post->comments = count(Post::where('post_id', $post->id)->get());
            $post->body = $this->checkForLinks($post->body);
            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
            
            $post->type = 'broadcast';
            if($post->post_id != null){
                $post->type = 'comment';
                array_push($broadcasts, $post);

                $original_post = Post::find($post->post_id);
                $user = User::select('name', 'image')->where('id', $original_post->user_id)->first();
                if($original_post->user_id == $post->user_id ){
                    $original_post->type = "thread";
                    $original_post->is_thread = true;
                    $get_posts = [];
                    array_push($get_posts, $original_post);
                    
                    $thread_posts = Post::where(['post_id'=> $post->post_id, 'user_id' => $post->user_id])->get();
                    foreach($thread_posts as $key => $thread_post){
                        if($post->id != $thread_post->id){
                            $thread_post->type = "thread";
                            array_push($get_posts, $thread_post);
                        }
                    }
                    if(count($get_posts) > 0){
                        foreach ($get_posts as $key => $post) {
                            $user = User::select('name', 'image')->where('id', $post->user_id)->first();
                            $post->user = $user;
                            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
                            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
                            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
                            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
                        
                            $post->timeago = $this->updateTime($post->created_at);
                            $post->info_display = 'retweets';
                            $post->comments = count(Post::where('post_id', $post->id)->get());
                            $post->body = $this->checkForLinks($post->body);
        
                            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
                            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
                            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
                            $post->is_comment = $post->post_id != null ? true : false; 
                            // if($post->is_comment){
                            //     $post->type = 'comment';
                            //     $original_post = Post::find($post->post_id);
                            //     if($original_post->user_id == $post->user_id){
                            //         $post->is_thread = true;
                            //         $post->type = "thread";
                            //     }
                            // }else{
                            //     if($post->post_id == null && count(Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get()) > 0){
                            //         $post->is_thread = true;
                            //         $post->type = "thread";
                            //     }
                            // }
                            array_push($broadcasts, $post);
                        }
                    }
                }else{
                    $original_post->user = $user;
                    $original_post->likes = count(PostsLike::where("post_id", $original_post->id)->get());
                    $original_post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;

                    $original_post->rebroadcasts = count(PostsRetweet::where("post_id", $original_post->id)->get());
                    $original_post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0 ? true : false;
                    
                    $original_post->comments = count(Post::where('post_id', $original_post->id)->get());
                
                    $original_post->timeago = $this->updateTime($original_post->created_at);
                    $original_post->info_display = 'retweets';
                    $original_post->body = $this->checkForLinks($original_post->body);
                    $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0? true : false;

                    $original_post->type = 'broadcast';        
                    $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                    $original_post->is_comment = $original_post->post_id != null ? true : false; 
                    if($original_post->is_comment){
                        $original_post->type = 'comment';
                        $original_post = Post::find($original_post->post_id);
                        if($original_post->user_id == $original_post->user_id){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }
                    }else{
                        if($original_post->post_id == null && count(Post::where(['post_id' => $original_post->id, 'user_id' => $original_post->user_id])->get()) > 0){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }

                    }
                    array_push($broadcasts, $original_post);
                }
            }else{
                if($post->post_id == null){
                    $get_posts = Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get();
                    if(count($get_posts) > 0){
                        $post->is_thread = true;
                        $post->type = "thread";
                        array_push($broadcasts, $post);
                        foreach ($get_posts as $key => $post) {
                            $user = User::select('name', 'image')->where('id', $post->user_id)->first();
                            $post->user = $user;
                            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
                            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
                            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
                            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
                        
                            $post->timeago = $this->updateTime($post->created_at);
                            $post->info_display = 'retweets';
                            $post->comments = count(Post::where('post_id', $post->id)->get());
                            $post->body = $this->checkForLinks($post->body);
        
                            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
                            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
                            $post->type = 'broadcast';        
                            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
                            $post->is_comment = $post->post_id != null ? true : false; 
                            if($post->is_comment){
                                $post->type = 'comment';
                            }
                            array_push($broadcasts, $post);
                        }
                    }else{
                        array_push($broadcasts, $post);
                    }
                }
            }
            $filtered_post = [];
            foreach($broadcasts as $key => $broadcast){
                $filtered_post[$broadcast->id] = $broadcast;
            }
            ksort($filtered_post);
        }
        // $broadcasts = [$post];
        $data = array(
            'status' => $status,
            'broadcasts' => $filtered_post
        );

        return new ShopResource($data);
    }

    public function newComment(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $this->validate($request, [
            'body' => ["required", "string"],
            'images' => ["nullable"],
            'post_id' => ["required", "integer"],
            'blogger_id' => ["required", "integer"]
        ]);
        $post = new Post();
        $post->user_id = $user_id;
        $post->post_id = $request->input('post_id');
        $post->body = $request->input('body');
        $post->save();
        $user = User::select('name', 'image')->where('id', $post->user_id)->first();
        $post->user = $user;
        $post->likes = count(PostsLike::where("post_id", $post->id)->get());
        $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

        $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
        $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
        
    
        $post->timeago = $this->updateTime($post->created_at);
        $post->info_display = 'retweets';
        $post->comments = count(Post::where('post_id', $post->id)->get());
        $post->body = $this->checkForLinks($post->body);

        $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
        $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;
        $post->type = "comment";
        
        if($request->input('blogger_id') != $user_id){
            $notification = new Notification;
            $notification->receiver_id = $request->input('blogger_id');
            $notification->sender_id = $user_id;
            $notification->type = 'comment-broadcast';
            $notification->data_id = json_encode($post->id);
            $notification->save(); 
        }

        $data = array(
            'status' => 200,
            'comment' => $post
        );
        return new ShopResource($data);
    }

    public function comments($id)
    {
        $user_id = auth('sanctum')->user()->id;
        $broadcasts = [];
        $original_post = Post::find($id);
        $comments = Post::where('post_id', $id)->orderBy('id', 'desc')->get();
        foreach ($comments as $key => $post) {
            $user = User::select('name', 'image')->where('id', $post->user_id)->first();
            $post->user= $user;
            $post->likes = count(PostsLike::where("post_id", $post->id)->get());
            $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

            $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
            $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;
            
            // $post->comment_replies = count(Post::where("comment_id", $post->id)->get());
        
            $post->timeago = $this->updateTime($post->created_at);
            $post->info_display = 'retweets';
            $post->comments = count(Post::where('post_id', $post->id)->get());
            $post->body = $this->checkForLinks($post->body);

            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
            $post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $post->user_id])->get()) > 0 ? true : false;

            $post->type = "comment";

            if($original_post->user_id != $post->user_id){
                array_push($broadcasts, $post);
            }
        }
        // $data = array(
        //     'status' => 200,
        //     'broadcasts'=> $broadcasts,
        //     'thread' => $broadcasts
        // );

        // return new ShopResource($data);

        return  ShopResource::collection($this->paginate($broadcasts));

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
        $broadcast = null;
        $this->validate($request, [
            'tweet' => ["required", "string"],
            'images' => ["nullable"],
        ]);
        $inputStatus = 0;
        $broadcast = new Post(); 
        $broadcast->user_id = $user_id;
        if ($request->hasFile('images')) {
            $broadcast_images = [];
            foreach ($request->file('images') as $key => $file) {
                // Get File name and extension
                $FileNameWithExt = $file->getClientOriginalName();
                $fileExt = $file->getClientOriginalExtension();
                // File name to store
                $fileNameToStore = time().'.'.$fileExt;
                // Store Image
                $path = $file->storeAs('public/broadcast_images', $fileNameToStore);
                array_push($broadcast_images, $fileNameToStore);
                list($width, $height) = getimagesize(storage_path('app/public/broadcast_images/'.$fileNameToStore));
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
                $img = Image::make(storage_path('app/public/broadcast_images/'.$fileNameToStore))->resize($newwidth, $newheight)->save( storage_path('app/public/broadcast_images/'.$fileNameToStore));
            }
            
            $broadcast->media = json_encode($broadcast_images);
            $inputStatus += 1;
        }
        if (is_string($request->input('tweet'))) {
            $broadcast->body = htmlspecialchars($request->input('tweet'));
            $inputStatus += 1;
            $broadcast->save();
            $user = User::select('name', 'image')->where('id', $broadcast->user_id)->first();
            $broadcast->user = $user;
            $broadcast->timeago = $this->updateTime($broadcast->created_at);
            $broadcast->likes = count(PostsLike::where("post_id", $broadcast->id)->get());
            $broadcast->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $broadcast->id])->get()) > 0 ? true : false;
            
            $broadcast->rebroadcasts = count(PostsRetweet::where("post_id", $broadcast->id)->get());
            $broadcast->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $broadcast->id])->get()) > 0 ? true : false;
            
            $broadcast->comments = count(Post::where('post_id', $broadcast->id)->get());

            $broadcast->body = $this->checkForLinks($broadcast->body);

            $broadcast->type = "broadcast";
        
        }
        if ($inputStatus == 1 || $inputStatus == 2) {
            $status = 200;
            $PUSH_NOTIFICATION_RECEIVERS = [];
            $PUSH_NOTIFICATION = [];
            $PUSH_BROADCAST = [];
            $followers = FollowingTable::where('receiver_id', $user_id)->get();
            if (count($followers) > 0) {
                foreach ($followers as $key => $follower) {
                    $notification = new Notification();
                    $notification->receiver_id = $follower->sender_id;
                    $notification->sender_id = $user_id;
                    $notification->type = 'new-broadcast';
                    $notification->data = \json_encode([$broadcast->id]);
                    $notification->save();
                    array_push($PUSH_NOTIFICATION_RECEIVERS, $follower->sender_id);
                    array_push($PUSH_NOTIFICATION, $notification);
                }
            }
            
        }
        $data = array(
            'status' => $status,
            'broadcast'=> $broadcast
        );
        array_push($PUSH_BROADCAST, $broadcast);
        broadcast(new NewBroadcast($PUSH_NOTIFICATION_RECEIVERS, $PUSH_BROADCAST));
        broadcast(new NewNotification($PUSH_NOTIFICATION_RECEIVERS, $PUSH_NOTIFICATION));
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
        $visited = User::find($id);
        if(!$visited){
            return redirect('/blog')->with('error', 'User not found');
        }
        $user_id = auth('sanctum')->user()->id;
        $user = User::find($user_id);
        $broadcasts = [];

        $retweets = PostsRetweet::where('user_id', $id)->get();
        foreach ($retweets as $key => $retweet) {
            $post = Post::where('id', $retweet->post_id)->first();
            $post->orderby_date = $retweet->created_at;
            $retweeter = User::select('id', 'name', 'image')->where('id', $id)->first();
            $post->retweeter = $retweeter;
            $post->is_following = true;
            array_push($broadcasts, $post);
        }

        $userTweets = Post::where('user_id', $id)->get();
        foreach ($userTweets as $key => $post) {
            $post->orderby_date = $post->created_at;
            $retweets = count(PostsRetweet::where(['user_id' => $id, 'post_id' => $post->id])->get());
            if($retweets > 0){
                $retweeter = User::select('id', 'name', 'image')->where('id', $id)->first();
                $post->retweeter = $retweeter;                
            }
            array_push($broadcasts, $post);
        }

        $newPosts = array();
        $post_counter = array();
        foreach ($broadcasts as $key => $post) {
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
            $post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0? true : false;
    
            $post->type = 'broadcast';        
            $post->is_comment = $post->post_id != null ? true : false; 
            if($post->is_comment){
                $post->type = 'comment';
                $original_post = Post::find($post->post_id);
                if($original_post->user_id == $post->user_id){
                    $post->is_thread = true;
                    $post->comment_is_thread = true;
                    $post->type = "thread";
                }else{
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
                    $original_post->bookmarked = count(PostsBookmark::where(["user_id" => $user_id, "post_id" => $original_post->id])->get()) > 0? true : false;

                    $original_post->type = 'broadcast';        
                    $original_post->is_following = count(FollowingTable::where(['sender_id' => $user_id, 'receiver_id' => $original_post->user_id])->get()) > 0 ? true : false;
                    $original_post->is_comment = $original_post->post_id != null ? true : false; 
                    if($original_post->is_comment){
                        $original_post->type = 'comment';
                        $original_post = Post::find($original_post->post_id);
                        if($original_post->user_id == $post->user_id){
                            $original_post->is_thread = true;
                            $post->comment_is_thread = true;
                            $original_post->type = "thread";
                        }
                    }else{
                        if($original_post->post_id == null && count(Post::where(['post_id' => $original_post->id, 'user_id' => $original_post->user_id])->get()) > 0){
                            $original_post->is_thread = true;
                            $original_post->type = "thread";
                        }
                    }
                    $post->original_post = $original_post;
                    
                }
            }else{
                if($post->post_id == null && count(Post::where(['post_id' => $post->id, 'user_id' => $post->user_id])->get()) > 0){
                    $post->is_thread = true;
                    $post->type = "thread";
                }
            }        

            if (array_key_exists($post->id, $post_counter)) {
                if ($post_counter[$post->id]['retweeter_id'] == null) {
                    if ($post_counter[$post->id]['user_id'] != $post->retweeter->id) {
                        $post_counter[$post->id]['retweets_count'] += 1;
                        $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                        $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                    }
                    else{
                        if($post_counter[$post->id]['retweets_count'] > 2){
                            $post_counter[$post->id]['retweets_count'] -= 1;
                        }else{
                            $post_counter[$post->id]['retweets_count'] = 1;
                        }
                        $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                        $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                    }
                }else{
                    if(isset($post->retweeter->id)){
                        if ($post_counter[$post->id]['retweeter_id'] != $post->retweeter->id) {
                            $post_counter[$post->id]['retweets_count'] += 1;
                            $post->retweets_count = $post_counter[$post->id]['retweets_count'];                            
                            $post_counter[$post->id]['retweeter_id'] = $post->retweeter->id;
                        }else{
                            $post_counter[$post->id]['retweets_count'] = 1;
                            $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                        }
                    }
                }
                unset($newPosts[$post_counter[$post->id]['time']]);
                $post_counter[$post->id]['time'] = strtotime($post->orderby_date);

                $newPosts[strtotime($post->orderby_date)] = $post;
            }else{
                $post_counter[$post->id] = array(
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'time' => strtotime($post->orderby_date),
                    'retweeter_id' => isset($post->retweeter->id) ? $post->retweeter->id : null,
                    'retweets_count' => isset($post->retweeter->id) ? 1 : 0,
                );
                $post->retweets_count = $post_counter[$post->id]['retweets_count'];
                $newPosts[strtotime($post->orderby_date)] = $post;
            } 
        }
        krsort($newPosts);

        foreach ($newPosts as $key => $value) {
            if($value->comment_is_thread){
                unset($newPosts[$key]);
            }
        }
        // $data = array(
        //     'status' => 200,
        //     'broadcasts' => $newPosts,
        // );
        if(count($newPosts) > 0){
            return  ShopResource::collection($this->paginate($newPosts));
        }
            
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