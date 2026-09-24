<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with courses.
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->with(['enrollments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->withCount(['enrollments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        return view('home.index', compact('courses'));
    }

    /**
     * Display the about/info page.
     */
    public function about()
    {
        return view('home.about');
    }
}
