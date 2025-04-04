<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserInventoryController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Login and logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile routes
    Route::get('/user/{username}', [UserController::class, 'getUserByUsername']);
    Route::post('/user/profile/update', [UserController::class, 'updateProfile']);

    // Return all users
    Route::get('/users', [UserController::class, 'index']);

    // Search users by username
    Route::get('/search/users', [UserController::class, 'searchUsers']);

    // Return posts of a user
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    // Posts routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Posts likes routes
    Route::post('/posts/{id}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{id}/like', [LikeController::class, 'destroy']);

    // Comments routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);

    // Stories routes
    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{id}', [StoryController::class, 'destroy'])->middleware('auth:sanctum');

    // Chats routes
    Route::get('/chats/private', [ChatController::class, 'getPrivateChat']);

    // Messages routes
    Route::post('/chats/{chatName}/messages', [MessageController::class, 'postMessages']);
    Route::get('/chats/{chatName}/messages', [MessageController::class, 'getMessages']);
    Route::patch('/messages/{message}', [MessageController::class, 'updateMessages']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // Contact route
    Route::post('/contact/submit', [ContactController::class, 'submit']);

    // Shop routes
    Route::get('/shop/categorized-items', [ItemController::class, 'categorizedItems']);
    Route::get('/shop/items/{id}', [ItemController::class, 'show']);

    // Purchase and inventory routes
    Route::get('/user/{id}/equipped-items', [UserController::class, 'getEquippedItems']);
    Route::post('/user/update-equipped-profile-frame', [UserController::class, 'updateEquippedProfileFrame']);
    Route::post('/user/update-equipped-profile-icon', [UserController::class, 'updateEquippedProfileIcon']);
    Route::post('/user/update-equipped-background', [UserController::class, 'updateEquippedBackground']); // Added
    Route::post('/user/update-equipped-animation', [UserController::class, 'updateEquippedAnimation']); // Added
    Route::post('/user/update-equipped-name-effect', [UserController::class, 'updateEquippedNameEffect']); // Added
    Route::post('/user/update-equipped-badge', [UserController::class, 'updateEquippedBadge']); // Added
    Route::post('/user/process-credit-purchase', [UserController::class, 'processCreditPurchase']);
    Route::post('/user/update-credits', [UserController::class, 'updateCredits']);
    Route::get('/user/{id}/inventory', [UserInventoryController::class, 'index']);
});
