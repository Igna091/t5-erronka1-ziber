<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

        return max(0, $this->capacity - $this->activeEnrollmentsCount());
    }

    /**
     * Number of active enrollments. Lists load it with
     * withCount(['enrollments' => fn ($q) => $q->where('status', 'active')])
     * so this doesn't run one query per course.
     */
    public function activeEnrollmentsCount(): int
    {
        if (array_key_exists('enrollments_count', $this->attributes)) {
            return (int) $this->attributes['enrollments_count'];
        }

        return $this->enrollments()->where('status', 'active')->count();
    }

    /**
     * Free spots as a text meter, e.g. "#########-" (all "#" when there is no limit).
     */
    public function seatMeter(int $width = 10): string
    {
        if (!$this->capacity) {
            return str_repeat('#', $width);
        }

        $lit = (int) round($this->available_spots / $this->capacity * $width);

        return str_repeat('#', $lit).str_repeat('-', $width - $lit);
    }

    /**
     * Short name for display, e.g. "diseno-ux-ui".
     */
    public function getSlugAttribute(): string
    {
        return Str::slug(str_replace('/', ' ', $this->name));
    }

    /**
     * Academic year for display, e.g. "2026/27".
     */
    public function getAcademicYearLabelAttribute(): ?string
    {
        if (!$this->academic_year || !preg_match('/^(\d{4})-\d{2}(\d{2})$/', $this->academic_year, $m)) {
            return $this->academic_year;
        }

        return $m[1].'/'.$m[2];
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
