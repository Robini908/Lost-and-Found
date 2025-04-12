<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'similarity_score',
        'matched_at',
        'processing_time_ms'
    ];

    protected $casts = [
        'similarity_score' => 'float',
        'matched_at' => 'datetime',
        'processing_time_ms' => 'float'
    ];

    /**
     * Get the lost item associated with this match.
     */
    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'lost_item_id');
    }

    /**
     * Get the found item associated with this match.
     */
    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'found_item_id');
    }
}
