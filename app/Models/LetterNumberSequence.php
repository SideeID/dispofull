<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Letter;

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

    public function generateLetterNumber(): string
    {
        $nextNumber = $this->getNextNumber();
        // Default fallback format updated to: 007/UB/R-XX/III/2025
        $format = $this->letterType->number_format ?? '{number}/UB/R-{code}/{month_roman}/{year}';

        $replacements = [
            '{number}' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            '{code}' => $this->letterType->code,
            '{department_code}' => $this->department->code ?? '',
            '{month}' => now()->format('m'),
            '{month_roman}' => $this->toRoman((int) now()->format('n')),
            '{year}' => $this->year,
            '{prefix}' => $this->prefix ?? '',
            '{suffix}' => $this->suffix ?? ''
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }

    public function generateUniqueLetterNumber(): string
    {
        do {
            $candidate = $this->generateLetterNumber();
            $exists = Letter::where('letter_number', $candidate)->exists();
        } while ($exists);

        return $candidate;
    }

    private function toRoman(int $month): string
    {
        $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
        return $map[$month] ?? (string)$month;
    }

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
