<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'academic_year',
        'duration_hours',
        'capacity',
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_hours' => 'integer',
            'capacity' => 'integer',
        ];
    }

    /**
     * Check if the course is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the subjects taught in this course.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)
            ->using(CourseSubject::class)
            ->withPivot(['id', 'teacher_id'])
            ->withTimestamps();
    }

    /**
     * Get the course subject rows (subject + teacher) of this course.
     */
    public function courseSubjects(): HasMany
    {
        return $this->hasMany(CourseSubject::class);
    }

    /**
     * Get the number of available spots.
     */
    public function getAvailableSpotsAttribute(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->enrollments()->where('status', 'active')->count());
    }

    /**
     * Check if the course has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        if ($this->capacity === null) {
            return true;
        }

        return $this->available_spots > 0;
    }
}
