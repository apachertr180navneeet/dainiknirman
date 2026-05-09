<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index(){
        $user = User::find(1);
        Auth::login($user);

        $token = $user->createToken('Testing')->accessToken;
        return response()->json($token, 200);
    }

    public function logout(){
        if(Auth::user()){
            Auth::user()->tokens()->delete();
        }
        return response()->json('Logout Successful', 200);
    }

    public function getUser(Request $request){
        $user = User::find($request->user_id);
        return response()->json($user, 200);
    }
}
