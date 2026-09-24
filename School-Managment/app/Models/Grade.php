<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    public const FINAL_EVALUATION = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'enrollment_id',
        'course_subject_id',
        'evaluation',
        'grade',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluation' => 'integer',
            'grade' => 'decimal:2',
        ];
    }

    /**
     * Get the enrollment (student in a course) this grade belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the subject of the course this grade belongs to.
     */
    public function courseSubject(): BelongsTo
    {
        return $this->belongsTo(CourseSubject::class);
    }

    /**
     * Check if this is the final evaluation.
     */
    public function isFinal(): bool
    {
        return $this->evaluation === self::FINAL_EVALUATION;
    }
}
