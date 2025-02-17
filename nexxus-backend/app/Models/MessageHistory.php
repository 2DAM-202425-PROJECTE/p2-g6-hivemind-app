<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id', 'user_id', 'old_content', 'action', 'changed_at'
    ];

    protected $dates = ['changed_at'];

    /**
     * Relación con el mensaje original.
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Relación con el usuario que editó/eliminó el mensaje.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
