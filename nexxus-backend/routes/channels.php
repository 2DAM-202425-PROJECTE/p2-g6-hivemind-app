<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('{chatName}', function ($user, $chatName) {
    $authorized = Chat::where('name', $chatName)
        ->whereHas('chat_user', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();

    Log::info('Channel authorization', [
        'user_id' => $user->id,
        'chat_name' => $chatName,
        'authorized' => $authorized,
    ]);

    return $authorized;
});
