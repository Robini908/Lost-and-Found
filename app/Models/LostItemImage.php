<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'image_path'
    ];

    protected $casts = [];

    /**
     * Get the lost item that owns the image.
     */
    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
