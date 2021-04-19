<?php

namespace App;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\MailResetPasswordToken;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use function GuzzleHttp\json_encode;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'telephone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function products()
    {
        # code...
        return $this->hasMany('App\Product');
    }

    public function broadcasts()
    {
        # code...
        return $this->hasMany('App\Post');
    }

    public function createUserData($user)
    {
        $user_id = auth('sanctum')->user()->id;
        $user->following = count(FollowingTable::where('sender_id', $user->id)->get());
        $user->followers = count(FollowingTable::where('receiver_id', $user->id)->get());
        if ($user_id != $user->id) {
            $user->is_following = count(FollowingTable::where(['receiver_id' => $user->id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
        }
        return $user;
    }
    
    public static function getUserDataFromName($user_name)
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::select('id', 'image', 'name', 'bio', 'geo_location')->where('name', $user_name)->first();
        $user->following = count(FollowingTable::where('sender_id', $user->id)->get());
        $user->followers = count(FollowingTable::where('receiver_id', $user->id)->get());
        if ($user_id != $user->id) {
            $user->is_following = count(FollowingTable::where(['receiver_id' => $user->id, 'sender_id' => $user_id])->get()) > 0 ? true : false;
        }
        return $user;
    }

    /**
     * Send a password reset email to the user
     */
    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new MailResetPasswordToken($token));
    // }
}
