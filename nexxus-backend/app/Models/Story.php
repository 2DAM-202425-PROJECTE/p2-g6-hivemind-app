<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;

    protected $table = 'stories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'description',
        'publish_date',
        'id_user',
        'file_path',
        'role', // Add role field to store the role associated with the story
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

    // Scope to filter stories by role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
