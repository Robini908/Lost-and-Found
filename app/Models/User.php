<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, HasTeams, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
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

    // Add the "creating" event listener
    protected static function booted()
    {
        static::created(function ($user) {
            // Assign the default "user" role to every new user
            $user->assignRole('user');
        });
    }

    // Relationships for lost items...
    public function reportedLostItems()
    {
        return $this->hasMany(LostItem::class, 'user_id');
    }

    public function foundLostItems()
    {
        return $this->hasMany(LostItem::class, 'found_by');
    }

    public function claimedLostItems()
    {
        return $this->hasMany(LostItem::class, 'claimed_by');
    }

    // Check if the current user is impersonating another user
    public function isImpersonating()
    {
        return session()->has('impersonator_id');
    }
}
