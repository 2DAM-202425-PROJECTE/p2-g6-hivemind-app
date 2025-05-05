<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

Broadcast::channel('{chatName}', function ($user, $chatName) {
    $authorized = Chat::where('name', $chatName)
        ->whereHas('chat_user', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
    return $authorized;
});
