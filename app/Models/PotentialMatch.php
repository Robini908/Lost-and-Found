<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotentialMatch extends Model
{
    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'similarity_score',
        'viewed',
        'confirmed'
    ];

    protected $casts = [
        'viewed' => 'boolean',
        'confirmed' => 'boolean',
        'similarity_score' => 'decimal:2'
    ];

    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'lost_item_id');
    }

    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'found_item_id');
    }
}
