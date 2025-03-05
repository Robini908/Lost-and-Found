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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Models\Impersonate;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, HasTeams, Notifiable, TwoFactorAuthenticatable, HasRoles, Impersonate;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'reward_points',
        'phone_number',
        'country_code',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'date_of_birth',
        'gender',
        'bio',
        'occupation',
        'company',
        'website',
        'social_links',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'id_type',
        'id_number',
        'profile_photo_path',
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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'social_links' => 'array',
        'date_of_birth' => 'date',
    ];

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

    /**
     * Check if the user can impersonate other users.
     * Only superadmins can impersonate other users.
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if the user can be impersonated.
     * Superadmins cannot be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('superadmin');
    }

    /**
     * Check if the current user is impersonating another user.
     */
    public function isImpersonating(): bool
    {
        return session()->has('impersonator_id');
    }

    /**
     * Get the reward histories for the user.
     */
    public function rewardHistories(): HasMany
    {
        return $this->hasMany(RewardHistory::class);
    }

    /**
     * Get the active reward points for the user.
     */
    public function getActiveRewardPoints(): int
    {
        return $this->rewardHistories()
            ->where('is_expired', false)
            ->selectRaw('SUM(CASE WHEN type IN ("earned", "bonus", "referral") THEN points ELSE -points END) as total_points')
            ->value('total_points') ?? 0;
    }

    /**
     * Decrement user's reward points.
     */
    public function decrementRewardPoints(int $points): bool
    {
        if ($points <= 0) {
            return false;
        }

        if ($this->reward_points < $points) {
            return false;
        }

        return $this->decrement('reward_points', $points);
    }

    /**
     * Add reward points to the user.
     */
    public function addRewardPoints(int $points): bool
    {
        if ($points <= 0) {
            return false;
        }

        if (!$this->reward_points) {
            $this->reward_points = 0;
        }

        $this->increment('reward_points', $points);

        Log::info('Added reward points to user', [
            'user_id' => $this->id,
            'points_added' => $points,
            'new_total' => $this->reward_points
        ]);

        return true;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
