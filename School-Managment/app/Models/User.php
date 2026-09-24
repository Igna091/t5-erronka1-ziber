<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role_id',
        'dni',
        'phone',
        'is_registered',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_registered' => 'boolean',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // New users are students unless a role is given
        static::creating(function (User $user) {
            $user->role_id ??= Role::where('name', Role::STUDENT)->value('id');
        });
    }

    /**
     * Get the role of the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has the given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->hasRole(Role::TEACHER);
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole(Role::STUDENT);
    }

    /**
     * Scope a query to users with the given role.
     */
    public function scopeWithRole(Builder $query, string $role): void
    {
        $query->whereHas('role', fn (Builder $q) => $q->where('name', $role));
    }

    /**
     * Scope a query to only students.
     */
    public function scopeStudents(Builder $query): void
    {
        $query->withRole(Role::STUDENT);
    }

    /**
     * Scope a query to only teachers.
     */
    public function scopeTeachers(Builder $query): void
    {
        $query->withRole(Role::TEACHER);
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . ($this->surname ?? ''));
    }

    /**
     * Get the initials of the user.
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->name ? mb_substr($this->name, 0, 1) : '';
        $surname = $this->surname ? mb_substr($this->surname, 0, 1) : '';
        return mb_strtoupper($name . $surname);
    }

    /**
     * Get the enrollments for this student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * IDs of the courses this student is actively enrolled in.
     *
     * @return list<int>
     */
    public function activeCourseIds(): array
    {
        return $this->enrollments()->where('status', 'active')->pluck('course_id')->all();
    }

    /**
     * Get the course subjects this teacher teaches.
     */
    public function taughtSubjects(): HasMany
    {
        return $this->hasMany(CourseSubject::class, 'teacher_id');
    }
}
