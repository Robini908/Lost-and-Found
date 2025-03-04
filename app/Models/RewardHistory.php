<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'points',
        'conversion_rate',
        'converted_amount',
        'currency',
        'description',
        'lost_item_id',
        'metadata',
        'expires_at',
        'is_expired'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'conversion_rate' => 'decimal:2',
        'converted_amount' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'is_expired' => 'boolean'
    ];

    /**
     * Get the user that owns the reward history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lost item associated with the reward history.
     */
    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class);
    }

    /**
     * Scope a query to only include earned points.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope a query to only include converted points.
     */
    public function scopeConverted($query)
    {
        return $query->where('type', 'converted');
    }

    /**
     * Scope a query to only include bonus points.
     */
    public function scopeBonus($query)
    {
        return $query->where('type', 'bonus');
    }

    /**
     * Scope a query to only include referral points.
     */
    public function scopeReferral($query)
    {
        return $query->where('type', 'referral');
    }

    /**
     * Scope a query to only include non-expired points.
     */
    public function scopeActive($query)
    {
        return $query->where('is_expired', false);
    }

    /**
     * Scope a query to only include points expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $days = (int)$days;
        return $query->where('expires_at', '>=', now())
                    ->where('expires_at', '<=', now()->addDays($days))
                    ->where('is_expired', false);
    }

    /**
     * Get the total points for a user.
     */
    public static function getUserPoints($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_expired', false)
            ->selectRaw('SUM(CASE WHEN type IN ("earned", "bonus", "referral") THEN points ELSE -points END) as total_points')
            ->value('total_points') ?? 0;
    }
}
