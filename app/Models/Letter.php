<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Letter extends Model
{
    protected $fillable = [
        'letter_number',
        'subject',
        'content',
        'letter_date',
        'direction',
        'status',
        'priority',
        'sender_name',
        'sender_address',
        'recipient_name',
        'recipient_address',
        'letter_type_id',
        'created_by',
        'from_department_id',
        'to_department_id',
        'original_file_path',
        'signed_file_path',
        'received_at',
        'processed_at',
        'archived_at',
        'notes'
    ];

    protected $casts = [
        'letter_date' => 'date',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'archived_at' => 'datetime'
    ];

    /**
     * Get the letter type that owns the letter.
     */
    public function letterType(): BelongsTo
    {
        return $this->belongsTo(LetterType::class);
    }

    /**
     * Get the user that created the letter.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the department that sent the letter.
     */
    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    /**
     * Get the department that receives the letter.
     */
    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    /**
     * Get the letter dispositions for the letter.
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(LetterDisposition::class);
    }

    /**
     * Get the letter attachments for the letter.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(LetterAttachment::class);
    }

    /**
     * Get the letter signatures for the letter.
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(LetterSignature::class);
    }

    /**
     * Check if the letter is incoming.
     */
    public function isIncoming(): bool
    {
        return $this->direction === 'incoming';
    }

    /**
     * Check if the letter is outgoing.
     */
    public function isOutgoing(): bool
    {
        return $this->direction === 'outgoing';
    }

    /**
     * Check if the letter is signed.
     */
    public function isSigned(): bool
    {
        return $this->signatures()->where('status', 'signed')->exists();
    }

    /**
     * Get the latest disposition.
     */
    public function latestDisposition()
    {
        return $this->dispositions()->latest()->first();
    }
}
