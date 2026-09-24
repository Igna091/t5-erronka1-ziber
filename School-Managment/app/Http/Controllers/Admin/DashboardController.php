<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $activeCount = ['enrollments' => fn ($query) => $query->where('status', 'active')];

        // Spots in active courses that have a capacity limit
        $limitedCourses = Course::where('status', 'active')->whereNotNull('capacity')->withCount($activeCount)->get();

        $stats = [
            'total_students' => User::students()->count(),
            'registered_students' => User::students()->where('is_registered', true)->count(),
            'pending_students' => User::students()->where('is_registered', false)->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
            'cancelled_enrollments' => Enrollment::where('status', 'cancelled')->count(),
            'total_seats' => $limitedCourses->sum('capacity'),
            'free_seats' => $limitedCourses->sum(fn (Course $course) => $course->available_spots),
        ];

        $recentStudents = User::students()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $courses = Course::withCount($activeCount)
            ->orderBy('status')
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentStudents', 'recentEnrollments', 'courses'));
    }
}
