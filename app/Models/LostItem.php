<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'status',
        'location',
        'date_lost',
        'date_found',
        'found_by',
        'claimed_by',
        'condition',
        'value',
        'item_type',
        'is_anonymous',
        'is_verified',
        'expiry_date',
        'geolocation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'geolocation' => 'array',
        'date_lost' => 'date',
        'date_found' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship to the user who reported the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the user who found the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foundBy()
    {
        return $this->belongsTo(User::class, 'found_by');
    }

    /**
     * Relationship to the user who claimed the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    /**
     * Relationship to the category of the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to the images of the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(LostItemImage::class);
    }
}
