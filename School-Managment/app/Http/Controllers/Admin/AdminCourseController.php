<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $course->update($this->validateCourse($request, $course));

        return redirect()->route('admin.courses.show', $course)
            ->with('success', "Curso «{$course->name}» actualizado correctamente.");
    }

    /**
     * Normalize and validate the course form.
     *
     * @return array<string, mixed>
     */
    private function validateCourse(Request $request, ?Course $course = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        // Capacity can't go below the students already enrolled
        $activeEnrollments = $course?->enrollments()->where('status', 'active')->count() ?? 0;

        return $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('courses', 'name')->where('academic_year', $course?->academic_year)->ignore($course),
            ],
            'code' => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($course)],
            'description' => ['nullable', 'string', 'max:5000'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'capacity' => ['nullable', 'integer', 'min:'.max(1, $activeEnrollments), 'max:1000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'name.unique' => 'Ya existe un curso con este nombre en el mismo año académico.',
            'code.unique' => 'Ya existe un curso con este código.',
            'capacity.min' => $activeEnrollments > 1
                ? "La capacidad no puede ser menor que las {$activeEnrollments} matrículas activas."
                : 'La capacidad debe ser al menos 1.',
            'end_date.after_or_equal' => 'La fecha de fin no puede ser anterior a la de inicio.',
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        $activeEnrollments = $course->enrollments()->where('status', 'active')->count();

        // Deleting would also delete the students' enrollments and grades
        if ($activeEnrollments > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', "No se puede eliminar «{$course->name}»: tiene {$activeEnrollments} matrícula(s) activa(s). Cancélalas o marca el curso como inactivo.");
        }

        // Cancelled enrollments, course subjects and grades are removed by the cascading foreign keys
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Curso «{$course->name}» eliminado correctamente.");
    }
}
