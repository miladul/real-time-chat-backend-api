<?php

use App\Events\UserTyping;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class,'register']);
Route::post('/login',    [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/me',    [AuthController::class,'me']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/messages/{user}', [ChatController::class, 'conversation']);
    Route::post('/messages', [ChatController::class, 'send']);

    // typing indicator event
    Route::post('/typing', function(Request $request){
        broadcast(new UserTyping(auth()->id(), $request->receiver_id));
        return response()->json(['ok' => true]);
    });
});
