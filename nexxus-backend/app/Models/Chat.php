<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_group', 'is_server', 'created_by', 'name', 'parent_id'
    ];

    /**
     * Relación con el usuario que creó el chat.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con los usuarios que participan en el chat.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user')->withTimestamps();
    }

    /**
     * Relación con los mensajes del chat.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relación con el chat padre (si este chat pertenece a un servidor).
     */
    public function parent()
    {
        return $this->belongsTo(Chat::class, 'parent_id');
    }

    /**
     * Relación con los subchats (si este es un servidor con múltiples chats dentro).
     */
    public function subchats()
    {
        return $this->hasMany(Chat::class, 'parent_id');
    }
}

