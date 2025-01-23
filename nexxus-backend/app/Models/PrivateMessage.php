<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
   protected $table = 'privatemessage';

   protected $fillable = [
        'id_sender',
        'id_receiver',
        'content',
        'send_date',
        'read_status',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'id_sender');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'id_receiver');
    }

    public function getContentAttribute($value)
    {
        return ucfirst($value);
    }

    public function setSendDateAttribute($value)
    {
        $this->attributes['send_date'] = \Carbon\Carbon::parse($value);
    }

    public function getReadStatusAttribute($value)
    {
        return $value ? 'Read' : 'Unread';
    }
}
