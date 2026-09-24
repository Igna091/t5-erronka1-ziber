<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::withCount(['enrollments' => function ($q) {
            $q->where('status', 'active');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->orderBy('name')->paginate(10);
        $courses->appends($request->query());

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.courses.index')
            ->with('success', 'Función de creación deshabilitada (Modo solo diseño).');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['enrollments.student']);
        $course->loadCount(['enrollments' => function ($q) {
            $q->where('status', 'active');
        }]);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        return redirect()->route('admin.courses.index')
            ->with('success', 'Función de edición deshabilitada (Modo solo diseño).');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        return redirect()->route('admin.courses.index')
            ->with('success', 'Función de eliminación deshabilitada (Modo solo diseño).');
    }
}
