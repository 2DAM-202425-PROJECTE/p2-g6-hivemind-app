<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

Broadcast::channel('{chatName}', function ($user, $chatName) {
    return Chat::where('name', $chatName)
        ->whereHas('chat_user', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
});
