<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $table = 'server';

    protected $fillable = [
        'name_server',
        'description',
        'owner',
        'creation_date',
        'photo_server',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function getNameServerAttribute($value)
    {
        return ucfirst($value);
    }

    public function setNameServerAttribute($value)
    {
        $this->attributes['name_server'] = ucfirst($value);
    }


    public function getDescriptionAttribute($value)
    {
        return ucfirst($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = ucfirst($value);
    }

    public function getCreationDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    public function setPhotoServerAttribute($value)
    {
        $this->attributes['photo_server'] = 'storage/' . $value;
    }
}
