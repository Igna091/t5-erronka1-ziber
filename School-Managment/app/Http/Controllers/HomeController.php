<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the home page with courses.
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->withCount(['enrollments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('start_date')
            ->orderBy('name')
            ->get();

        $enrolledIds = Auth::user()?->isStudent() ? Auth::user()->activeCourseIds() : [];
        $academicYear = $courses->pluck('academic_year_label')->filter()->first();

        return view('home.index', compact('courses', 'enrolledIds', 'academicYear'));
    }

    /**
     * Display the about/info page.
     */
    public function about()
    {
        return view('home.about');
    }
}
