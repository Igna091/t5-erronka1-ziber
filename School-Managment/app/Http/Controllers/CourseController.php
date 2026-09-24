<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display all available courses (public).
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->withCount(['enrollments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        return view('student.courses', compact('courses'));
    }

    /**
     * Display a single course.
     */
    public function show(Course $course)
    {
        $course->loadCount(['enrollments' => function ($query) {
            $query->where('status', 'active');
        }]);

        $isEnrolled = false;
        if (Auth::check() && Auth::user()->isStudent()) {
            $isEnrolled = Enrollment::where('student_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('student.course-detail', compact('course', 'isEnrolled'));
    }

    /**
     * Enroll a student in a course.
     */
    public function enroll(Request $request, Course $course)
    {
        $studentId = Auth::id();

        $error = DB::transaction(function () use ($course, $studentId) {
            // Lock the course row so two students can't take the last spot at once
            $course = Course::whereKey($course->id)->lockForUpdate()->first();

            if (!$course->isActive()) {
                return 'Este curso no está disponible para matrícula.';
            }

            if ($course->end_date && $course->end_date->lt(today())) {
                return 'Este curso ya ha finalizado.';
            }

            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment?->status === 'active') {
                return 'Ya estás matriculado/a en este curso.';
            }

            if (!$course->hasAvailableSpots()) {
                return 'No quedan plazas disponibles en este curso.';
            }

            // A cancelled enrollment is reactivated (student + course is unique)
            if ($enrollment) {
                $enrollment->update(['status' => 'active', 'enrolled_at' => now()]);
            } else {
                Enrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                ]);
            }

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('student.enrollments')
            ->with('success', "Te has matriculado en «{$course->name}» correctamente.");
    }

    /**
     * Show student's enrollments.
     */
    public function myEnrollments()
    {
        $enrollments = Enrollment::where('student_id', Auth::id())
            ->with('course')
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return view('student.my-enrollments', compact('enrollments'));
    }

    /**
     * Show student profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $enrollmentCount = Enrollment::where('student_id', $user->id)
            ->where('status', 'active')
            ->count();

        return view('student.profile', compact('user', 'enrollmentCount'));
    }
}
