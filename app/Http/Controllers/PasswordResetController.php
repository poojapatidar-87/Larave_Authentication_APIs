<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    //method to send email
    public function send_reset_password_email(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->email;

        //check if user's email exists or not
        $user = User::where('email',$email)->first();
        if(!$user){
            return response([
                'message'=>'Email does not exists',
                'status'=>'failed'
            ],404);
        }

        // Generate Token
        $token = Str::random(60);

        // Saving data to  password reset table
        PasswordReset::create([
            'email'=>$email,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);

        // Sending EMail with password reset view
       Mail::send('reset',['token'=>$token],function(Message $message)use ($email){
            $message->subject('Reset Your password');
            $message->to($email);
       });

        return response([
            'message'=>'Password Reset Email Sent...Check Your Email',
            'status'=>'success'
        ],200);
    }

    public function reset(Request $request){
        $request->validate([
            'password'=>'required|confirmed',
        ]);

        $passwordreset = PasswordReset::where('token',$token)->first();
        if(!passwordreset){
            return response([
                'message'=>'Token is Invalid or Expired',
                'status'=>'failed'
            ],404);
        }

        $user = User::where('email',$passwordreset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        //Delete the token after resetting password
        PasswordReset::where('email',$user->email)->delete();

        return response([
            'message'=>'Password reset successfully',
            'status'=>'success'
        ],200);
    }
}
