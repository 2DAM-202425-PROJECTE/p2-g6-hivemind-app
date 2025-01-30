<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Server;

class Channel extends Model
{
    use HasFactory;

    protected $table = 'channel';

    protected $fillable = [
        'name_channel',
        'type_channel',
        'id_server',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'id_server');
    }

    public function getNameChannelAttribute($value)
    {
        return ucfirst($value);
    }

    public function setTypeChannelAttribute($value)
    {
        $this->attributes['type_channel'] = strtolower($value);
    }
}
