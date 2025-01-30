<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';

    protected $fillable = [
        'id_post',
        'id_user',
        'comment_text',
        'comment_date',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'id_post');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getCommentTextAttribute($value)
    {
        return ucfirst($value);
    }

    public function getCommentDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }

    public function setCommentDateAttribute($value)
    {
        $this->attributes['comment_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
}
