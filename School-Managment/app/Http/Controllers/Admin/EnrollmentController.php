<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exceptions\EnrollmentException;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'cancelled'])],
        ]);

        $query = Enrollment::with(['student', 'course']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            // Grouped so the OR doesn't bypass the status filter below
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('surname', 'like', "%{$search}%");
                })->orWhereHas('course', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(10);
        $enrollments->appends($request->query());

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for enrolling a student in a course.
     * Accepts ?student_id= and ?course_id= to preselect them.
     */
    public function create(Request $request)
    {
        $students = User::students()->orderBy('surname')->orderBy('name')->get();

        // Only courses that accept enrollments: active and not finished
        $courses = Course::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', today()))
            ->withCount(['enrollments' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        $selectedStudentId = $request->integer('student_id') ?: null;
        $selectedCourseId = $request->integer('course_id') ?: null;

        return view('admin.enrollments.create', compact('students', 'courses', 'selectedStudentId', 'selectedCourseId'));
    }

    /**
     * Enroll a student in a course.
     */
    public function store(Request $request, EnrollmentService $enrollments)
    {
        $validated = $request->validate([
            'student_id' => [
                'required', 'integer',
                Rule::exists('users', 'id')->where('role_id', Role::where('name', Role::STUDENT)->value('id')),
            ],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ], [
            'student_id.required' => 'Selecciona un alumno/a.',
            'student_id.exists' => 'El alumno/a seleccionado no existe.',
            'course_id.required' => 'Selecciona un curso.',
            'course_id.exists' => 'El curso seleccionado no existe.',
        ]);

        $student = User::findOrFail($validated['student_id']);
        $course = Course::findOrFail($validated['course_id']);

        try {
            $enrollments->enroll($student, $course);
        } catch (EnrollmentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', "{$student->full_name} matriculado/a en «{$course->name}» correctamente.");
    }

    /**
     * Cancel an enrollment.
     */
    public function cancel(Enrollment $enrollment, EnrollmentService $enrollments)
    {
        try {
            $enrollments->cancel($enrollment);
        } catch (EnrollmentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Matrícula de {$enrollment->student->full_name} en «{$enrollment->course->name}» cancelada.");
    }

    /**
     * Reactivate an enrollment.
     */
    public function reactivate(Enrollment $enrollment, EnrollmentService $enrollments)
    {
        try {
            $enrollments->reactivate($enrollment);
        } catch (EnrollmentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Matrícula de {$enrollment->student->full_name} en «{$enrollment->course->name}» reactivada.");
    }
}
