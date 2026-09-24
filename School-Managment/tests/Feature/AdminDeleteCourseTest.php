<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteCourseTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $this->course = Course::create(['name' => 'DAW', 'code' => 'DAW-1', 'status' => 'active']);
    }

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => 'Test',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function enroll(string $status): Enrollment
    {
        return Enrollment::create([
            'student_id' => $this->user(Role::STUDENT, uniqid().'@educenter.es')->id,
            'course_id' => $this->course->id,
            'status' => $status,
        ]);
    }

    public function test_admin_can_delete_course_without_enrollments(): void
    {
        $subject = Subject::create(['code' => 'PROG', 'name' => 'Programación', 'hours' => 200]);
        $this->course->subjects()->attach($subject->id);

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success', 'Curso «DAW» eliminado correctamente.');

        $this->assertModelMissing($this->course);
        $this->assertSame(0, CourseSubject::count());
        // Subjects are shared between courses, so they are kept
        $this->assertModelExists($subject);
    }

    public function test_course_with_active_enrollments_cannot_be_deleted(): void
    {
        $enrollment = $this->enroll('active');

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($this->course);
        $this->assertModelExists($enrollment);
    }

    public function test_cancelled_enrollments_do_not_block_deletion(): void
    {
        $enrollment = $this->enroll('cancelled');

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertSessionHas('success');

        $this->assertModelMissing($this->course);
        $this->assertModelMissing($enrollment);
    }

    public function test_student_cannot_delete_courses(): void
    {
        $this->actingAs($this->user(Role::STUDENT, 'alumno@educenter.es'))
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertForbidden();

        $this->assertModelExists($this->course);
    }

    public function test_guest_cannot_delete_courses(): void
    {
        $this->delete(route('admin.courses.destroy', $this->course))->assertRedirect(route('login'));

        $this->assertModelExists($this->course);
    }
}
