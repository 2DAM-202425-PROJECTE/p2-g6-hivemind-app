<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravolt\Avatar\Facade as Avatar;

class User extends Authenticatable
{
    use CrudTrait;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'profile_photo_path', 'banner_photo_path', 'description', 'email_verified_at',
        'credits', 'equipped_profile_icon_path', 'equipped_custom_banner', 'equipped_background_path',
        'equipped_name_effect_path', 'equipped_badge_path', 'equipped_profile_font_path','equipped_profile_icon_color',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'id_user');
    }

    protected function defaultProfilePhotoUrl()
    {
        return Avatar::create($this->name)->toBase64();
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return $this->defaultProfilePhotoUrl();
    }

    public function getBannerPhotoPathAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function getEquippedProfileIconPathAttribute($value)
    {
        return $value;
    }

    public function getEquippedProfileFramePathAttribute($value)
    {
        return $value;
    }

    public function getEquippedBackgroundPathAttribute($value)
    {
        return $value;
    }

    public function getEquippedAnimationPathAttribute($value)
    {
        return $value;
    }

    public function getEquippedNameEffectPathAttribute($value)
    {
        return $value;
    }

    public function getEquippedBadgePathAttribute($value)
    {
        return $value;
    }

    protected function getInitials()
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        if (count($words) >= 1) {
            $initials .= strtoupper(substr($words[0], 0, 1));
        }
        if (count($words) >= 2) {
            $initials .= strtoupper(substr($words[count($words) - 1], 0, 1));
        }

        return $initials ?: 'U';
    }

    public function inventory()
    {
        return $this->hasMany(UserInventory::class);
    }
}
