<?php

namespace App\Http\Controllers;

use App\Exceptions\EnrollmentException;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Inactive courses are hidden, except for admins and students enrolled in them
        if (!$course->isActive()) {
            $user = Auth::user();
            $canSee = $user && ($user->isAdmin() || $course->enrollments()->where('student_id', $user->id)->exists());

            abort_unless($canSee, 404);
        }

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
    public function enroll(Request $request, Course $course, EnrollmentService $enrollments)
    {
        try {
            $enrollments->enroll(Auth::user(), $course);
        } catch (EnrollmentException $e) {
            return back()->with('error', $e->getMessage());
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
