<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['registered', 'pending'])],
        ]);

        $query = User::students();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
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
        $validated = $this->validateStudent($request);

        // The student sets their own password when activating the account in /register.
        // Until then, a random unknown password (hashed with argon2id) blocks the login.
        $student = User::create([
            ...$validated,
            'password' => Str::password(32),
            'role_id' => Role::where('name', Role::STUDENT)->value('id'),
            'is_registered' => false,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Alumno creado. Ya puede activar su cuenta en el registro con su email y DNI.');
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
        if (!$student->isStudent()) {
            abort(404);
        }

        $student->update($this->validateStudent($request, $student));

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Datos del alumno/a actualizados correctamente.');
    }

    /**
     * Normalize and validate the student form (create and edit).
     *
     * @return array<string, mixed>
     */
    private function validateStudent(Request $request, ?User $student = null): array
    {
        $request->merge([
            'dni' => strtoupper(trim((string) $request->input('dni'))),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student)],
            'dni' => ['required', 'string', 'max:20', Rule::unique('users', 'dni')->ignore($student)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'email.unique' => 'Ya existe un usuario con este email.',
            'dni.unique' => 'Ya existe un usuario con este DNI.',
        ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $name = $student->full_name;

        DB::transaction(function () use ($student) {
            // Log the student out of any open session
            DB::table('sessions')->where('user_id', $student->id)->delete();

            // Enrollments (and their grades) are removed by the cascading foreign keys
            $student->delete();
        });

        return redirect()->route('admin.students.index')
            ->with('success', "Alumno/a {$name} eliminado/a correctamente.");
    }
}
