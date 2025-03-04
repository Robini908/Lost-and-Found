<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSecureIds;
use Illuminate\Support\Facades\Hash;

class ItemClaim extends Model
{
    use HasFactory, HasSecureIds;

    protected $fillable = [
        'user_id',
        'lost_item_id',
        'found_item_id',
        'status',
        'claim_details',
        'verification_method',
        'verification_notes',
        'verifier_id',
        'verified_at',
        'rejection_notes',
        'rejected_at'
    ];

    protected $casts = [
        'claim_details' => 'array',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    protected $hidden = ['id', 'user_id', 'verifier_id']; // Hide sensitive fields

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class, 'lost_item_id');
    }

    public function foundItem()
    {
        return $this->belongsTo(LostItem::class, 'found_item_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    // Status checks
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isExpired()
    {
        return $this->verification_expires_at && now()->isAfter($this->verification_expires_at);
    }

    // Verification methods
    public function verify($verifier, $notes, $details = [])
    {
        // Add verification attempt logging
        activity()
            ->causedBy($verifier)
            ->performedOn($this)
            ->withProperties(['details' => $details])
            ->log('claim_verification_attempt');

        // Validate verification attempt
        if ($this->hasRecentVerificationAttempt()) {
            throw new \Exception('Please wait before attempting another verification');
        }

        $this->update([
            'status' => 'verified',
            'verifier_id' => $verifier->id,
            'verification_notes' => $notes,
            'verification_method' => $details['method'] ?? null,
            'verified_at' => now(),
            'verification_hash' => Hash::make(json_encode([
                'verifier' => $verifier->id,
                'timestamp' => now()->timestamp,
                'claim' => $this->id
            ]))
        ]);
    }

    protected function hasRecentVerificationAttempt(): bool
    {
        return cache()->get('verification_attempt_' . $this->id, 0) > 5;
    }

    public function reject($verifier, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'verifier_id' => $verifier->id,
            'rejection_notes' => $notes,
            'rejected_at' => now(),
        ]);
    }

    public function requireInPersonVerification($expirationDate)
    {
        $this->update([
            'requires_in_person' => true,
            'in_person_expiration' => $expirationDate,
        ]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequiresInPersonVerification($query)
    {
        return $query->where('requires_in_person', true);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('in_person_expiration')
            ->where('in_person_expiration', '<', now());
    }
}
