<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //register method
    public function register(Request $request)
    {
        //validating data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'tc' => 'required',
        ]);
        // checking if user already exists or not
        if (User::where('email', $request->email)->first()) {
            return response([
                'message' => 'Email Already Exists',
                'status' => 'failed',
            ], 200);
        }
        // preparing object to insert data in table
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tc' => json_decode($request->tc),
        ]);

        $token = $user->createToken($request->email)->plainTextToken;
        // returns this message when registration is successful
        return response([
            'token' => $token,
            'message' => 'Registration Successful',
            'status' => 'success',
        ], 201);
    }

    //login method
    public function login(Request $request)
    {
        //validating data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // checking if user already exists or not
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            // returns this message when registration is successful
            return response([
                'token' => $token,
                'message' => 'Login Successful',
                'status' => 'success',
            ], 201);
        }
        return response([
            'message' => 'the Provided Credentilas are icorrect.',
            'status' => 'failed'
        ],401);
    }

    // logout method
    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout Successful',
            'status' => 'success'
        ],200);
    }

    // get data of logged in-user
    public function logged_user(){
        $loggeduser=auth()->user();
        return response([
            'user'=>$loggeduser,
            'message' => 'Logged User Data',
            'status' => 'success'
        ],200);
    }


    // Change password method
    public function change_password(Request $request){
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $loggeduser = auth()->user();
        $loggeduser->password = Hash::make($request->password);
        $loggeduser->save();
        return response([
            'message' => 'Password Changed Successfully',
            'status' => 'success'
        ],200);
    }
    
}
