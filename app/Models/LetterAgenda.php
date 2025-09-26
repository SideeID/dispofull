<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterAgenda extends Model
{
    protected $fillable = [
        'title',
        'description',
        'agenda_date',
        'start_date',
        'end_date',
        'type',
        'department_id',
        'created_by',
        'status',
        'pdf_path',
        'filters'
    ];

    protected $casts = [
        'agenda_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'filters' => 'array'
    ];

    /**
     * Get the department that owns the agenda.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created the agenda.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the agenda is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the agenda is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Get the letters that match the agenda filters.
     */
    public function getFilteredLetters()
    {
        $query = Letter::query()
            ->whereBetween('letter_date', [$this->start_date, $this->end_date]);

        if ($this->filters) {
            // Apply letter type filter
            if (isset($this->filters['letter_types']) && !empty($this->filters['letter_types'])) {
                $query->whereIn('letter_type_id', $this->filters['letter_types']);
            }

            // Apply department filter
            if (isset($this->filters['departments']) && !empty($this->filters['departments'])) {
                $query->where(function ($q) {
                    $q->whereIn('from_department_id', $this->filters['departments'])
                      ->orWhereIn('to_department_id', $this->filters['departments']);
                });
            }

            // Apply direction filter
            if (isset($this->filters['direction']) && !empty($this->filters['direction'])) {
                $query->where('direction', $this->filters['direction']);
            }

            // Apply status filter
            if (isset($this->filters['status']) && !empty($this->filters['status'])) {
                $query->whereIn('status', $this->filters['status']);
            }
        }

        return $query->with(['letterType', 'fromDepartment', 'toDepartment', 'creator'])
                     ->orderBy('letter_date')
                     ->get();
    }

    /**
     * Generate PDF for the agenda.
     */
    public function generatePdf(): string
    {
        // Implementation will depend on your PDF generation library
        // This is just a placeholder
        $letters = $this->getFilteredLetters();

        // Generate PDF logic here
        // Return the path to the generated PDF

        return 'path/to/generated/pdf';
    }
}
