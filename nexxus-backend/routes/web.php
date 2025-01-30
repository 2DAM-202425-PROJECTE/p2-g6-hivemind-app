<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/forbidden', function () {
    return response()->view('errors.forbidden', [], 403);
})->name('forbidden');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:ROLE_ADMIN',
])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
});
