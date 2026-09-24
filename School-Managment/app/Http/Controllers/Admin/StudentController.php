<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filter by registration status
        if ($request->filled('status')) {
            if ($request->status === 'registered') {
                $query->where('is_registered', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_registered', false);
            }
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(10);
        $students->appends($request->query());

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.students.index')
            ->with('success', 'Función de creación deshabilitada (Modo solo diseño).');
    }

    /**
     * Display the specified student.
     */
    public function show(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $student->load(['enrollments.course']);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, User $student)
    {
        return redirect()->route('admin.students.index')
            ->with('success', 'Función de edición deshabilitada (Modo solo diseño).');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(User $student)
    {
        return redirect()->route('admin.students.index')
            ->with('success', 'Función de eliminación deshabilitada (Modo solo diseño).');
    }
}
