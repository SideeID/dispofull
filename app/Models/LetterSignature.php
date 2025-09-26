<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterSignature extends Model
{
    protected $fillable = [
        'letter_id',
        'user_id',
        'signature_type',
        'signature_path',
        'signature_data',
        'signed_at',
        'ip_address',
        'user_agent',
        'status',
        'notes'
    ];

    protected $casts = [
        'signed_at' => 'datetime'
    ];

    /**
     * Get the letter that owns the signature.
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }

    /**
     * Get the user who signed the letter.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the signature is digital.
     */
    public function isDigital(): bool
    {
        return $this->signature_type === 'digital';
    }

    /**
     * Check if the signature is electronic.
     */
    public function isElectronic(): bool
    {
        return $this->signature_type === 'electronic';
    }

    /**
     * Check if the signature is signed.
     */
    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    /**
     * Mark signature as signed.
     */
    public function markAsSigned(): void
    {
        $this->update([
            'status' => 'signed',
            'signed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Mark signature as rejected.
     */
    public function markAsRejected(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $reason
        ]);
    }
}
