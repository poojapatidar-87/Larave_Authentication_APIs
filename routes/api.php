<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;

// Public Routes
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/send_reset_password_email',[PasswordResetController::class,'send_reset_password_email']);
Route::post('/reset_password/{token}',[PasswordResetController::class,'reset']);


// Private Routes
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/loggeduser',[UserController::class,'logged_user']);
    Route::post('/changepassword',[UserController::class,'change_password']);
});
