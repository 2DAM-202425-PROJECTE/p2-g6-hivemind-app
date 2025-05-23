<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'verification_token',
        'expires_at',
    ];

    public $timestamps = false;

    protected $hidden = [
        'password',
    ];
}
