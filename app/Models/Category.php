<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relationship to LostItem model
    public function lostItems()
    {
        return $this->hasMany(LostItem::class);
    }
}
