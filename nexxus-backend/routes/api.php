<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // return all users
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    // retun posts of a user
    Route::get('/users/{id}/posts', [App\Http\Controllers\UserController::class, 'posts']);

    Route::get('/posts', [App\Http\Controllers\PostController::class, 'index']);
});
