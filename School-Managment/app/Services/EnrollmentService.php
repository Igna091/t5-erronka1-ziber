<?php

namespace App\Services;

use App\Exceptions\EnrollmentException;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Enroll a student in a course, or reactivate a cancelled enrollment.
     *
     * @throws EnrollmentException
     */
    public function enroll(User $student, Course $course): Enrollment
    {
        if (!$student->isStudent()) {
            throw new EnrollmentException(__('Solo se puede matricular a alumnos.'));
        }

        return DB::transaction(function () use ($student, $course) {
            // Lock the course row so two students can't take the last spot at once
            $course = Course::whereKey($course->id)->lockForUpdate()->firstOrFail();

            if (!$course->isActive()) {
                throw new EnrollmentException(__('Este curso no está disponible para matrícula.'));
            }

            if ($course->end_date && $course->end_date->lt(today())) {
                throw new EnrollmentException(__('Este curso ya ha finalizado.'));
            }

            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment?->status === 'active') {
                throw new EnrollmentException(__('Ya existe una matrícula activa en este curso.'));
            }

            if (!$course->hasAvailableSpots()) {
                throw new EnrollmentException(__('No quedan plazas disponibles en este curso.'));
            }

            // A cancelled enrollment is reactivated (student + course is unique)
            if ($enrollment) {
                $enrollment->update(['status' => 'active', 'enrolled_at' => now()]);

                return $enrollment;
            }

            return Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        });
    }

    /**
     * Cancel an active enrollment. Grades are kept in case it is reactivated.
     *
     * @throws EnrollmentException
     */
    public function cancel(Enrollment $enrollment): Enrollment
    {
        if ($enrollment->status !== 'active') {
            throw new EnrollmentException(__('Esta matrícula ya está cancelada.'));
        }

        $enrollment->update(['status' => 'cancelled']);

        return $enrollment;
    }

    /**
     * Reactivate a cancelled enrollment, checking the same rules as a new one
     * (course active, not finished, spots available).
     *
     * @throws EnrollmentException
     */
    public function reactivate(Enrollment $enrollment): Enrollment
    {
        if ($enrollment->status === 'active') {
            throw new EnrollmentException(__('Esta matrícula ya está activa.'));
        }

        return $this->enroll($enrollment->student, $enrollment->course);
    }
}
