<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'hours',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hours' => 'integer',
        ];
    }

    /**
     * Get the courses that include this subject.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->using(CourseSubject::class)
            ->withPivot(['id', 'teacher_id'])
            ->withTimestamps();
    }
}
