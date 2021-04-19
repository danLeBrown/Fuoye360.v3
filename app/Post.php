<?php

namespace App;

use App\Traits\CheckForLinksTrait;
use App\Traits\TimeagoTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use CheckForLinksTrait, TimeagoTrait;
    protected $table  = 'posts';

    public function createPostData($post)
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::select('name', 'image')->where('id', $post->user_id)->first();
        $post->user = $user;
        $post->likes = count(PostsLike::where("post_id", $post->id)->get());
        $post->is_liked = count(PostsLike::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

        $post->rebroadcasts = count(PostsRetweet::where("post_id", $post->id)->get());
        $post->is_rebroadcast = count(PostsRetweet::where(["user_id" => $user_id, "post_id" => $post->id])->get()) > 0 ? true : false;

        $post->comments = count(Post::where('post_id', $post->id)->get());
        $post->media = json_decode($post->media, true);
        $post->timeago = $this->timeago($post->created_at);
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
                $original_post->media = json_decode($original_post->media, true);

                $original_post->timeago = $this->timeago($original_post->created_at);
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
        
        return $post;
    }
}
