<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'number_format',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the letters for this letter type.
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * Get the letter number sequences for this letter type.
     */
    public function letterNumberSequences(): HasMany
    {
        return $this->hasMany(LetterNumberSequence::class);
    }
}
