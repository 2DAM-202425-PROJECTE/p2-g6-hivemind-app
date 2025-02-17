<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatUser extends Pivot
{
    use HasFactory;

    protected $table = 'chat_user';

    protected $fillable = [
        'chat_id', 'user_id', 'role', 'joined_at', 'is_muted'
    ];

    protected $dates = ['joined_at'];

    /**
     * Relación con el chat.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Relación con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
