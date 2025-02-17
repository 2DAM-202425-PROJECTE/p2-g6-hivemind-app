<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // return all users
    Route::get('/users', [UserController::class, 'index']);
    // retun posts of a user
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    Route::get('/posts', [App\Http\Controllers\PostController::class, 'index']);
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store']);
    Route::post('/posts/{id}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{id}/like', [LikeController::class, 'destroy']);

    // Chats routes
    Route::post('/chats/private', [ChatController::class, 'storePrivateChat']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
//    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // Messages routes
    Route::post('/chats/{chat}/messages', [ChatController::class, 'store']);
//    Route::patch('/messages/{message}', [ChatController::class, 'update']);
//    Route::delete('/messages/{message}', [ChatController::class, 'destroy']);


});
