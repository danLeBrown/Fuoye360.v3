<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\validation\ValidationException;
use App\Http\Resources\AuthResource;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile_number' => ['required', 'string',],
            'password' => ['required', 'string', 'min:8']
        ]);
        User::create([
            'name' => $request->username,
            'email'=> $request->email,
            'telephone' => $request->mobile_number,
            'password'=> Hash::make($request->password)
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        $user = User::where('email', $request->email)->first();
        if (Auth::attempt($request->only('email', 'password' ))) {
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status_code' => 200,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        }
        throw  validationException::withMessages([
            'email' =>  ['Oops! Check Email and Password and try again'],
            'password' => ['Oops! Password is incorrect']
        ]);
    }
}
