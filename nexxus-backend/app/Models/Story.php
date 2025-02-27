<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{

    use HasFactory;

    protected $fillable = [
        'description',
        'publish_date',
        'id_user',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
}
