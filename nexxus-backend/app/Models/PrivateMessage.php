<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
   use HasFactory;

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

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = ucfirst($value);
    }

    public function getSendDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    public function setSendDateAttribute($value)
    {
        $this->attributes['send_date'] = \Carbon\Carbon::parse($value);
    }

    public function getReadStatusAttribute($value)
    {
        return $value ? 'Read' : 'Unread';
    }

    public function setReadStatusAttribute($value)
    {
        $this->attributes['read_status'] = $value === 'Read' ? 1 : 0;
    }
}
