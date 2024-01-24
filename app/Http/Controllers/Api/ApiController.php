<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // Register API (POST)
    public function register(Request $request){

        // Data validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        // Create User
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // Login API (POST)
    public function login(Request $request){

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Email check
        $user = User::where("email", $request->email)->first();

        // If user exists, then concept of Password validation
        if(!empty($user)){

            if(Hash::check($request->password, $user->password)){

                // User exists
                $token = $user->createToken("myToken")->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "token" => $token
                ]);
            }else{

                return response()->json([
                    "status" => true,
                    "message" => "Password didn't match"
                ]);
            }
        }else{

            return response()->json([
                "status" => false,
                "message" => "Invalid login details"
            ]);
        }

        // Sanctum token value
    }

    // Profile API (GET)
    public function profile(){

        $user = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile information",
            "user" => $user,
            "userID" => auth()->user()->id
        ]);
    }

    // Logout API (GET)
    public function logout(){
        
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User logout successfully"
        ]);
    }
}
