<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCancelReactivateEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN);
        $this->course = Course::create(['name' => 'DAW', 'code' => 'DAW-1', 'capacity' => 1, 'status' => 'active']);
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'María',
            'surname' => 'García',
            'email' => uniqid().'@educenter.es',
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function enrollment(string $status): Enrollment
    {
        return Enrollment::create([
            'student_id' => $this->user(Role::STUDENT)->id,
            'course_id' => $this->course->id,
            'status' => $status,
        ]);
    }

    private function patchAs(User $user, string $route, Enrollment $enrollment)
    {
        return $this->actingAs($user)
            ->from(route('admin.enrollments.index', ['status' => 'active', 'page' => 2]))
            ->patch(route($route, $enrollment));
    }

    public function test_admin_can_cancel_enrollment_and_grades_are_kept(): void
    {
        $enrollment = $this->enrollment('active');
        $subject = Subject::create(['code' => 'PROG', 'name' => 'Programación', 'hours' => 200]);
        $this->course->subjects()->attach($subject->id);
        Grade::create([
            'enrollment_id' => $enrollment->id,
            'course_subject_id' => CourseSubject::first()->id,
            'evaluation' => 1,
            'grade' => 8,
        ]);

        $this->patchAs($this->admin, 'admin.enrollments.cancel', $enrollment)
            // Stays on the same page with the same filters
            ->assertRedirect(route('admin.enrollments.index', ['status' => 'active', 'page' => 2]))
            ->assertSessionHas('success', 'Matrícula de María García en «DAW» cancelada.');

        $this->assertSame('cancelled', $enrollment->refresh()->status);
        $this->assertSame(1, Grade::count());
    }

    public function test_cancelling_frees_a_spot(): void
    {
        $this->patchAs($this->admin, 'admin.enrollments.cancel', $this->enrollment('active'));

        $this->assertTrue($this->course->refresh()->hasAvailableSpots());
    }

    public function test_cancelled_enrollment_cannot_be_cancelled_again(): void
    {
        $enrollment = $this->enrollment('cancelled');

        $this->patchAs($this->admin, 'admin.enrollments.cancel', $enrollment)
            ->assertSessionHas('error', 'Esta matrícula ya está cancelada.');
    }

    public function test_admin_can_reactivate_enrollment(): void
    {
        $enrollment = $this->enrollment('cancelled');

        $this->patchAs($this->admin, 'admin.enrollments.reactivate', $enrollment)
            ->assertSessionHas('success', 'Matrícula de María García en «DAW» reactivada.');

        $this->assertSame('active', $enrollment->refresh()->status);
        $this->assertSame(1, Enrollment::count());
    }

    public function test_active_enrollment_cannot_be_reactivated(): void
    {
        $enrollment = $this->enrollment('active');

        $this->patchAs($this->admin, 'admin.enrollments.reactivate', $enrollment)
            ->assertSessionHas('error', 'Esta matrícula ya está activa.');
    }

    public function test_reactivation_is_blocked_when_course_is_full(): void
    {
        $cancelled = $this->enrollment('cancelled');
        $this->enrollment('active'); // takes the only spot

        $this->patchAs($this->admin, 'admin.enrollments.reactivate', $cancelled)
            ->assertSessionHas('error', 'No quedan plazas disponibles en este curso.');

        $this->assertSame('cancelled', $cancelled->refresh()->status);
    }

    public function test_reactivation_is_blocked_when_course_is_inactive(): void
    {
        $cancelled = $this->enrollment('cancelled');
        $this->course->update(['status' => 'inactive']);

        $this->patchAs($this->admin, 'admin.enrollments.reactivate', $cancelled)
            ->assertSessionHas('error', 'Este curso no está disponible para matrícula.');

        $this->assertSame('cancelled', $cancelled->refresh()->status);
    }

    public function test_non_admin_cannot_cancel_or_reactivate(): void
    {
        $active = $this->enrollment('active');
        $student = $active->student;

        $this->patchAs($student, 'admin.enrollments.cancel', $active)->assertForbidden();
        $this->patchAs($student, 'admin.enrollments.reactivate', $active)->assertForbidden();

        $this->assertSame('active', $active->refresh()->status);
    }
}
