<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the users for the department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the letters from this department.
     */
    public function lettersFrom(): HasMany
    {
        return $this->hasMany(Letter::class, 'from_department_id');
    }

    /**
     * Get the letters to this department.
     */
    public function lettersTo(): HasMany
    {
        return $this->hasMany(Letter::class, 'to_department_id');
    }

    /**
     * Get the letter number sequences for this department.
     */
    public function letterNumberSequences(): HasMany
    {
        return $this->hasMany(LetterNumberSequence::class);
    }

    /**
     * Get the letter agendas for this department.
     */
    public function letterAgendas(): HasMany
    {
        return $this->hasMany(LetterAgenda::class);
    }

    /**
     * Get the letter dispositions to this department.
     */
    public function letterDispositions(): HasMany
    {
        return $this->hasMany(LetterDisposition::class, 'to_department_id');
    }
}
