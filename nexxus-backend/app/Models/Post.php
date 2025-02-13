<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';
    
    protected $fillable = [
        'content',
        'publish_date',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getContentAttribute($value)
    {
        return ucfirst($value);
    }

    public function getPublishDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = \Carbon\Carbon::parse($value);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
