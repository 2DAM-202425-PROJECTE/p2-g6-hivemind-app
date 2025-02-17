<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_id', 'user_id', 'content', 'is_edited', 'deleted_at', 'read_at'
    ];

    protected $dates = ['deleted_at', 'read_at'];

    /**
     * Relación con el chat al que pertenece el mensaje.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Relación con el usuario que envió el mensaje.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el historial de ediciones del mensaje.
     */
    public function histories()
    {
        return $this->hasMany(MessageHistory::class);
    }
}
