<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Services\AccountActivation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class TeacherController extends Controller
{
    /**
     * Show the form for creating a new teacher.
     */
    public function create(Request $request)
    {
        $courses = Course::with(['courseSubjects.subject', 'courseSubjects.teacher'])
            ->orderBy('name')
            ->get();

        // Coming from a course page: that course is already chosen
        $selectedCourseId = $request->integer('course_id') ?: null;

        return view('admin.teachers.create', compact('courses', 'selectedCourseId'));
    }

    /**
     * Store a new teacher and assign them to a course: the whole course, some of
     * its subjects and/or a new subject created for it.
     */
    public function store(Request $request, AccountActivation $activation)
    {
        $validated = $this->validateTeacher($request);

        $teacher = DB::transaction(function () use ($validated) {
            // The teacher sets their own password with the activation link sent by email.
            // Until then, a random unknown password (hashed with argon2id) blocks the login.
            $teacher = User::create([
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'email' => $validated['email'],
                'dni' => $validated['dni'],
                'phone' => $validated['phone'] ?? null,
                'password' => Str::password(32),
                'role_id' => Role::where('name', Role::TEACHER)->value('id'),
                'is_registered' => false,
            ]);

            $course = Course::findOrFail($validated['course_id']);

            if ($validated['whole_course'] ?? false) {
                $course->teachers()->attach($teacher);
            }

            // A subject has one teacher per course: the previous one (if any) is replaced
            if (!empty($validated['course_subjects'])) {
                $course->courseSubjects()->whereKey($validated['course_subjects'])->update(['teacher_id' => $teacher->id]);
            }

            if (!empty($validated['subject_code'])) {
                $subject = Subject::create([
                    'code' => $validated['subject_code'],
                    'name' => $validated['subject_name'],
                    'hours' => $validated['subject_hours'],
                ]);
                $course->subjects()->attach($subject, ['teacher_id' => $teacher->id]);
            }

            return $teacher;
        });

        $redirect = redirect()->route('admin.teachers.show', $teacher);

        try {
            $activation->send($teacher);
        } catch (TransportExceptionInterface $e) {
            report($e);

            return $redirect
                ->with('success', __('Profesor/a creado/a.'))
                ->with('error', __('No se pudo enviar el email de activación. Revisa la configuración del correo y usa «Reenviar email de activación».'));
        }

        return $redirect->with('success', __('Profesor/a creado/a. Le hemos enviado un email a :email para activar su cuenta.', ['email' => $teacher->email]));
    }

    /**
     * Display the specified teacher with what they teach.
     */
    public function show(User $teacher, AccountActivation $activation)
    {
        if (!$teacher->isTeacher()) {
            abort(404);
        }

        $teacher->load([
            'taughtCourses' => fn ($q) => $q->orderBy('name'),
            'taughtSubjects.course',
            'taughtSubjects.subject',
        ]);

        // Activation status while the teacher hasn't chosen a password
        $activationInfo = $teacher->is_registered ? null : $activation->statusOf($teacher);

        return view('admin.teachers.show', compact('teacher', 'activationInfo'));
    }

    /**
     * Send a new activation email to a teacher who hasn't activated the account.
     */
    public function resendActivation(User $teacher, AccountActivation $activation)
    {
        if (!$teacher->isTeacher()) {
            abort(404);
        }

        if ($teacher->is_registered) {
            return back()->with('error', __('Este profesor/a ya ha activado su cuenta.'));
        }

        try {
            if (!$activation->send($teacher)) {
                return back()->with('error', __('Ya se ha enviado un email hace menos de un minuto. Espera un poco antes de reenviarlo.'));
            }
        } catch (TransportExceptionInterface $e) {
            report($e);

            return back()->with('error', __('No se pudo enviar el email de activación. Revisa la configuración del correo.'));
        }

        return back()->with('success', __('Email de activación reenviado a :email. El enlace anterior ya no funciona.', ['email' => $teacher->email]));
    }

    /**
     * Normalize and validate the new teacher form.
     *
     * @return array<string, mixed>
     */
    private function validateTeacher(Request $request): array
    {
        $request->merge([
            'dni' => strtoupper(trim((string) $request->input('dni'))),
            'email' => strtolower(trim((string) $request->input('email'))),
            'subject_code' => strtoupper(trim((string) $request->input('subject_code'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'dni' => ['required', 'string', 'max:20', Rule::unique('users', 'dni')],
            'phone' => ['nullable', 'string', 'max:20'],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'whole_course' => ['nullable', 'boolean'],
            'course_subjects' => ['nullable', 'array'],
            // Only subjects of the chosen course
            'course_subjects.*' => ['integer', Rule::exists('course_subject', 'id')->where('course_id', $request->integer('course_id'))],
            // New subject: the three fields go together
            'subject_code' => ['nullable', 'required_with:subject_name,subject_hours', 'string', 'max:20', Rule::unique('subjects', 'code')],
            'subject_name' => ['nullable', 'required_with:subject_code,subject_hours', 'string', 'max:100'],
            'subject_hours' => ['nullable', 'required_with:subject_code,subject_name', 'integer', 'min:1', 'max:10000'],
        ], [
            'email.unique' => __('Ya existe un usuario con este email.'),
            'dni.unique' => __('Ya existe un usuario con este DNI.'),
            'course_id.required' => __('Elige un curso.'),
            'course_subjects.*.exists' => __('Marca solo asignaturas del curso elegido.'),
            'subject_code.unique' => __('Ya existe una asignatura con este código.'),
        ]);

        if (!($validated['whole_course'] ?? false) && empty($validated['course_subjects']) && empty($validated['subject_code'])) {
            throw ValidationException::withMessages([
                'assignment' => __('Asígnale el curso entero, alguna de sus asignaturas o una asignatura nueva.'),
            ]);
        }

        return $validated;
    }
}
