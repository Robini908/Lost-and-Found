<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LostItem extends Model
{
    use HasFactory;

    /**
     * Constants for item types
     */
    const TYPE_REPORTED = 'reported';
    const TYPE_SEARCHED = 'searched';
    const TYPE_FOUND = 'found';

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
        'location_type',
        'location_address',
        'location_lat',
        'location_lng',
        'area',
        'landmarks',
        'location_lost',
        'location_found',
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
        'matched_found_item_id',
        'brand',
        'model',
        'color',
        'serial_number',
        'estimated_value',
        'currency',
        'found_at',
        'claimed_at',
        'returned_at',
        'expires_at',
        'additional_details'
    ];

    protected $attributes = [
        'status' => 'lost',
        'is_verified' => false,
        'is_anonymous' => false,
        'currency' => 'USD',
        'item_type' => self::TYPE_REPORTED,
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
        'expires_at' => 'datetime',
        'found_at' => 'datetime',
        'claimed_at' => 'datetime',
        'returned_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_anonymous' => 'boolean',
        'value' => 'float',
        'estimated_value' => 'decimal:2',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'additional_details' => 'json'
    ];

    /**
     * Get the user that owns the lost item.
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
    public function claimedByUser()
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

    /**
     * Relationship to the matched found item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matchedFoundItem()
    {
        return $this->belongsTo(LostItem::class, 'matched_found_item_id');
    }

    /**
     * Relationship to the items that this item has matched.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchedItems()
    {
        return $this->hasMany(LostItem::class, 'matched_found_item_id');
    }

    /**
     * Get potential matches where this item is the lost item
     */
    public function potentialMatches()
    {
        return $this->hasMany(PotentialMatch::class, 'lost_item_id');
    }

    /**
     * Get potential matches where this item is the found item
     */
    public function foundMatches()
    {
        return $this->hasMany(PotentialMatch::class, 'found_item_id');
    }

    /**
     * Get all potential matches (both as lost and found item)
     */
    public function allMatches()
    {
        return $this->potentialMatches->merge($this->foundMatches);
    }
}
