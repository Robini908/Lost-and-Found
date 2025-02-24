<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'image_path',
    ];

    // Relationship to LostItem model
    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }
}
