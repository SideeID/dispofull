<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterNumberSequence extends Model
{
    protected $fillable = [
        'letter_type_id',
        'department_id',
        'year',
        'last_number',
        'prefix',
        'suffix'
    ];

    /**
     * Get the letter type that owns the sequence.
     */
    public function letterType(): BelongsTo
    {
        return $this->belongsTo(LetterType::class);
    }

    /**
     * Get the department that owns the sequence.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the next number in sequence.
     */
    public function getNextNumber(): int
    {
        $this->increment('last_number');
        return $this->last_number;
    }

    /**
     * Generate letter number based on format.
     */
    public function generateLetterNumber(): string
    {
        $nextNumber = $this->getNextNumber();
        $format = $this->letterType->number_format ?? '{number}/{code}/{month}/{year}';

        $replacements = [
            '{number}' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            '{code}' => $this->letterType->code,
            '{department_code}' => $this->department->code ?? '',
            '{month}' => now()->format('m'),
            '{year}' => $this->year,
            '{prefix}' => $this->prefix ?? '',
            '{suffix}' => $this->suffix ?? ''
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }

    /**
     * Find or create sequence for given parameters.
     */
    public static function findOrCreate(int $letterTypeId, ?int $departmentId = null, ?int $year = null): self
    {
        $year = $year ?? now()->year;

        return static::firstOrCreate([
            'letter_type_id' => $letterTypeId,
            'department_id' => $departmentId,
            'year' => $year
        ], [
            'last_number' => 0
        ]);
    }
}
