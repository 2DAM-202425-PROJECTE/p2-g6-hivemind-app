<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id', 
        'user_id', 
        'content',
        'media_path',
        'parent_id' // Allow parent_id to be mass assigned
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getContentAttribute($value)
    {
        return ucfirst($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
