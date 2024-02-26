<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Namshi\JOSE\JWT;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    public function index(Request $request){

        //set validasi
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        // get data email dan password
        $credentials = $request->only('email','password');

        // cek 
        if (!$token = auth()->guard('api_customer')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ],401);
        }

        //response login success dan generat token
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
            'token' => $token
        ],200);

    }

    public function getUser(){
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
        ],200);

    }

    public function refresh()
    {
        //refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set user degan "token" baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header "Authorization" dengan type Bearer + "token" baru

    //response data "user" dengan "token" baru
        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $refreshToken,  
        ], 200);

    }

    public function logout(){        
        //remoce token JWT
        $removeToken= JWTAuth::invalidate(JWTAuth::getToken());        
        //response "success" logout
        return response()->json([
            'success' => true,
        ], 200);    
    }
    
} 
