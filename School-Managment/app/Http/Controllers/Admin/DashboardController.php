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
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'registered_students' => User::where('role', 'student')->where('is_registered', true)->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
        ];

        $recentStudents = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $courses = Course::withCount(['enrollments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('name')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentStudents', 'recentEnrollments', 'courses'));
    }
}
