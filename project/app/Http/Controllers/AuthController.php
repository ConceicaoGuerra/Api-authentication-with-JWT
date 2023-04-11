<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
     
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 422);
        }

        $token = auth('api')->attempt($validator->validated());
        
        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 401);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    
    public function logout(Request $request) 
    {
        try {
            auth('api')->logout();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, cannot logout'
            ], 500);
        }
    }

   
    public function userProfile(Request $request) 
    {
        return response()->json([
            'status' => true,
            'message' => 'User found',
            'data' => auth('api')->user()
        ], 200);
    }

    
    public function refresh(Request $request) 
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

  
    protected function respondWithToken($token)
    {
        $minutes = auth('api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at
        ], 200);
    }
}
