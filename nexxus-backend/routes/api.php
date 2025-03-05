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
use App\Http\Controllers\StoryController;
use App\Models\Story;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function ()
{
    //login and logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // return all users
    Route::get('/users', [UserController::class, 'index']);
    // retun posts of a user
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    // posts routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // posts likes routes
    Route::post('/posts/{id}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{id}/like', [LikeController::class, 'destroy']);

    // comments routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);

    Route::get('/stories', [StoryController::class, 'index']);

    // chats routes
    Route::get('/chats/private', [ChatController::class, 'getPrivateChat']);
    // Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // messages routes
    Route::post('/chats/{chatName}/messages', [MessageController::class, 'postMessages']);
    Route::get('/chats/{chatName}/messages', [MessageController::class, 'getMessages']);

    Route::patch('/messages/{message}', [MessageController::class, 'updateMessages']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    Route::post('/contact/submit', [ContactController::class, 'submit']);
});

Route::get('/api/random-users', function (Request $request) {
    $users = User::inRandomOrder()->limit(4)->get(['id', 'name', 'profile_photo_url']);
    return response()->json($users);
});

