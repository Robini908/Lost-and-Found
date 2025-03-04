<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LostItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'image_path'
    ];

    protected $appends = ['url'];

    protected $casts = [
        'lost_item_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the lost item that owns the image.
     */
    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Delete the image file from storage when the model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            Storage::disk('public')->delete($image->image_path);
        });
    }
}
