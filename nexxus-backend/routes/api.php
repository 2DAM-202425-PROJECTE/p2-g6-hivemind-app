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
use App\Models\Story;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/pending', [AuthController::class, 'registerPending']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::get('/check-verification', [AuthController::class, 'checkVerification']);
Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

Route::middleware('auth:sanctum')->group(function ()
{
    //logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile routes
    Route::get('/user/{username}', [UserController::class, 'getUserByUsername']);
    Route::post('/user/profile/update', [UserController::class, 'updateProfile']);

    // return all users
    Route::get('/users', [UserController::class, 'index']);

    // search users by username
    Route::get('/search/users', [UserController::class, 'searchUsers']);

    // retun posts of a user
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    // posts routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // posts likes routes
    Route::post('/posts/{id}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{id}/like', [LikeController::class, 'destroy']);


    // comments routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);

    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{id}', [StoryController::class, 'destroy'])->middleware('auth:sanctum');

    // Chats routes
    Route::get('/chats/private', [ChatController::class, 'getPrivateChat']);
    // Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // messages routes
    Route::post('/chats/{chatName}/messages', [MessageController::class, 'postMessages']);
    Route::get('/chats/{chatName}/messages', [MessageController::class, 'getMessages']);

    Route::patch('/messages/{message}', [MessageController::class, 'updateMessages']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    Route::post('/contact/submit', [ContactController::class, 'submit']);

    //Shop routes
    Route::get('/shop/categorized-items', [ItemController::class, 'categorizedItems']);
    Route::get('/shop/items/{id}', [ItemController::class, 'show']);

    //Purchase routes
    Route::post('/user/update-equipped-profile-icon', [UserController::class, 'updateEquippedProfileIcon']);
    Route::post('/user/process-credit-purchase', [UserController::class, 'processCreditPurchase']);
    Route::post('/user/update-credits', [UserController::class, 'updateCredits']);
    Route::post('/user/inventory', [UserController::class, 'saveToInventory']);
    Route::get('/user/{id}/inventory', [UserInventoryController::class, 'index']);
});


