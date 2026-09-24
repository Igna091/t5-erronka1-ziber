<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class AdminEnrollStudentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $student;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $this->student = $this->user(Role::STUDENT, 'maria@educenter.es');
        $this->course = $this->course();
    }

    private function user(string $role, string $email, bool $registered = true): User
    {
        return User::create([
            'name' => 'María',
            'surname' => 'García',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => $registered,
        ]);
    }

    private function course(array $attributes = []): Course
    {
        return Course::create(array_merge([
            'name' => 'Desarrollo Web',
            'code' => 'DW-'.uniqid(),
            'capacity' => 20,
            'status' => 'active',
        ], $attributes));
    }

    private function store(array $data = [])
    {
        return $this->actingAs($this->admin)
            ->from(route('admin.enrollments.create'))
            ->post(route('admin.enrollments.store'), array_merge([
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
            ], $data));
    }

    public function test_admin_can_enroll_student(): void
    {
        $this->store()
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('success', 'María García matriculado/a en «Desarrollo Web» correctamente.');

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_enroll_student_who_has_not_activated_account(): void
    {
        $pending = $this->user(Role::STUDENT, 'ana@educenter.es', registered: false);

        $this->store(['student_id' => $pending->id])->assertSessionHas('success');
    }

    public function test_cancelled_enrollment_is_reactivated(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'cancelled',
        ]);

        $this->store()->assertSessionHas('success');

        $this->assertSame('active', $enrollment->refresh()->status);
        $this->assertSame(1, Enrollment::count());
    }

    public function test_business_rules_are_applied(): void
    {
        $this->store();
        $this->store()
            ->assertRedirect(route('admin.enrollments.create'))
            ->assertSessionHas('error', 'Ya existe una matrícula activa en este curso.');

        $full = $this->course(['capacity' => 1]);
        Enrollment::create(['student_id' => $this->user(Role::STUDENT, 'x@educenter.es')->id, 'course_id' => $full->id, 'status' => 'active']);
        $this->store(['course_id' => $full->id])->assertSessionHas('error', 'No quedan plazas disponibles en este curso.');

        $inactive = $this->course(['status' => 'inactive']);
        $this->store(['course_id' => $inactive->id])->assertSessionHas('error', 'Este curso no está disponible para matrícula.');

        $finished = $this->course(['end_date' => today()->subDay()]);
        $this->store(['course_id' => $finished->id])->assertSessionHas('error', 'Este curso ya ha finalizado.');

        $this->assertSame(2, Enrollment::count());
    }

    public function test_input_is_kept_when_enrollment_fails(): void
    {
        $inactive = $this->course(['status' => 'inactive']);

        $this->store(['course_id' => $inactive->id])->assertSessionHasInput('course_id', $inactive->id);
    }

    public function test_only_students_can_be_enrolled(): void
    {
        $teacher = $this->user(Role::TEACHER, 'profe@educenter.es');

        $this->store(['student_id' => $teacher->id])->assertSessionHasErrors('student_id');
        $this->store(['student_id' => $this->admin->id])->assertSessionHasErrors('student_id');

        $this->assertSame(0, Enrollment::count());
    }

    public function test_student_and_course_are_required_and_must_exist(): void
    {
        $this->store(['student_id' => '', 'course_id' => ''])
            ->assertSessionHasErrors(['student_id' => 'Selecciona un alumno/a.', 'course_id' => 'Selecciona un curso.']);

        $this->store(['student_id' => 999, 'course_id' => 999])
            ->assertSessionHasErrors(['student_id', 'course_id']);
    }

    public function test_non_admin_cannot_enroll_students(): void
    {
        $this->actingAs($this->student)
            ->post(route('admin.enrollments.store'), ['student_id' => $this->student->id, 'course_id' => $this->course->id])
            ->assertForbidden();

        $this->assertSame(0, Enrollment::count());
    }

    public function test_create_form_receives_students_and_open_courses(): void
    {
        // The real view is built by the frontend; use a stub to inspect the data
        $dir = storage_path('framework/testing/views');
        File::ensureDirectoryExists("{$dir}/admin/enrollments");
        File::put("{$dir}/admin/enrollments/create.blade.php", 'stub');
        View::addLocation($dir);

        $this->user(Role::TEACHER, 'profe@educenter.es');
        $inactive = $this->course(['status' => 'inactive']);
        $finished = $this->course(['end_date' => today()->subDay()]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.enrollments.create', ['course_id' => $this->course->id]))
            ->assertOk()
            ->assertViewHas('selectedCourseId', $this->course->id)
            ->assertViewHas('selectedStudentId', null);

        $this->assertSame([$this->student->id], $response->viewData('students')->pluck('id')->all());
        $courseIds = $response->viewData('courses')->pluck('id');
        $this->assertTrue($courseIds->contains($this->course->id));
        $this->assertFalse($courseIds->contains($inactive->id));
        $this->assertFalse($courseIds->contains($finished->id));
        $this->assertSame(0, $response->viewData('courses')->first()->enrollments_count);

        File::deleteDirectory($dir);
    }
}
