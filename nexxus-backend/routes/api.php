<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Chats routes
    Route::get('/chats/private', [ChatController::class, 'getPrivateChat']);
//    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // Messages routes
    Route::post('/chats/{chatName}/messages', [MessageController::class, 'postMessages']);
    Route::get('/chats/{chatName}/messages', [MessageController::class, 'getMessages']);
//    Route::patch('/messages/{message}', [MessageController::class, 'update']);
//    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    Route::post('/contact/submit', [ContactController::class, 'submit']);
});

Route::get('/api/random-users', function (Request $request) {
    $users = User::inRandomOrder()->limit(4)->get(['id', 'name', 'profile_photo_url']);
    return response()->json($users);
});
