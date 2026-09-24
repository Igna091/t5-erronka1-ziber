<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollTest extends TestCase
{
    use RefreshDatabase;

    private int $users = 0;

    private function student(): User
    {
        $this->users++;

        return User::create([
            'name' => 'Alumno',
            'email' => "alumno{$this->users}@educenter.es",
            'password' => 'password',
            'is_registered' => true,
        ]);
    }

    private function course(array $attributes = []): Course
    {
        return Course::create(array_merge([
            'name' => 'Desarrollo Web',
            'code' => 'DW-'.uniqid(),
            'capacity' => 2,
            'status' => 'active',
            'start_date' => today()->addMonth(),
            'end_date' => today()->addYear(),
        ], $attributes));
    }

    private function enroll(User $student, Course $course)
    {
        return $this->actingAs($student)
            ->from(route('courses.show', $course))
            ->post(route('courses.enroll', $course));
    }

    public function test_student_can_enroll(): void
    {
        $student = $this->student();
        $course = $this->course();

        $this->enroll($student, $course)
            ->assertRedirect(route('student.enrollments'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
    }

    public function test_student_cannot_enroll_twice(): void
    {
        $student = $this->student();
        $course = $this->course();

        $this->enroll($student, $course);
        $this->enroll($student, $course)->assertSessionHas('error', 'Ya existe una matrícula activa en este curso.');

        $this->assertSame(1, Enrollment::count());
    }

    public function test_cancelled_enrollment_is_reactivated(): void
    {
        $student = $this->student();
        $course = $this->course();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'cancelled',
        ]);

        $this->enroll($student, $course)->assertSessionHas('success');

        $this->assertSame('active', $enrollment->refresh()->status);
        $this->assertSame(1, Enrollment::count());
    }

    public function test_full_course_rejects_enrollment(): void
    {
        $course = $this->course(['capacity' => 1]);
        $this->enroll($this->student(), $course);

        $this->enroll($this->student(), $course)
            ->assertSessionHas('error', 'No quedan plazas disponibles en este curso.');

        $this->assertSame(1, Enrollment::count());
    }

    public function test_cancelled_enrollments_do_not_take_spots(): void
    {
        $course = $this->course(['capacity' => 1]);
        Enrollment::create(['student_id' => $this->student()->id, 'course_id' => $course->id, 'status' => 'cancelled']);

        $this->enroll($this->student(), $course)->assertSessionHas('success');
    }

    public function test_course_without_capacity_is_unlimited(): void
    {
        $course = $this->course(['capacity' => null]);

        for ($i = 0; $i < 3; $i++) {
            $this->enroll($this->student(), $course)->assertSessionHas('success');
        }
    }

    public function test_inactive_course_rejects_enrollment(): void
    {
        $course = $this->course(['status' => 'inactive']);

        $this->enroll($this->student(), $course)
            ->assertSessionHas('error', 'Este curso no está disponible para matrícula.');

        $this->assertSame(0, Enrollment::count());
    }

    public function test_finished_course_rejects_enrollment(): void
    {
        $course = $this->course(['start_date' => today()->subYear(), 'end_date' => today()->subDay()]);

        $this->enroll($this->student(), $course)->assertSessionHas('error', 'Este curso ya ha finalizado.');
    }

    public function test_course_ending_today_still_accepts_enrollment(): void
    {
        $course = $this->course(['start_date' => today()->subYear(), 'end_date' => today()]);

        $this->enroll($this->student(), $course)->assertSessionHas('success');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post(route('courses.enroll', $this->course()))->assertRedirect(route('login'));

        $this->assertSame(0, Enrollment::count());
    }

    public function test_admin_cannot_enroll(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@educenter.es',
            'password' => 'password',
            'role_id' => Role::where('name', Role::ADMIN)->value('id'),
            'is_registered' => true,
        ]);

        $this->enroll($admin, $this->course())->assertForbidden();

        $this->assertSame(0, Enrollment::count());
    }
}
