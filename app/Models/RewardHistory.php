<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardHistory extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'points',
        'description',
        'lost_item_id',
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
}
