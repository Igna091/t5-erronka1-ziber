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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDeleteStudentTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    public function test_admin_can_delete_student_with_enrollments_and_grades(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $student = $this->user(Role::STUDENT, 'alumno@educenter.es');

        $course = Course::create(['name' => 'DAW', 'code' => 'DAW-1', 'status' => 'active']);
        $subject = Subject::create(['code' => 'PROG', 'name' => 'Programación', 'hours' => 200]);
        $course->subjects()->attach($subject->id);
        $enrollment = Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);
        Grade::create([
            'enrollment_id' => $enrollment->id,
            'course_subject_id' => CourseSubject::first()->id,
            'evaluation' => 1,
            'grade' => 7.5,
        ]);
        DB::table('sessions')->insert([
            'id' => 'student-session',
            'user_id' => $student->id,
            'payload' => '',
            'last_activity' => time(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.students.destroy', $student))
            ->assertRedirect(route('admin.students.index'))
            ->assertSessionHas('success', 'Alumno/a Test User eliminado/a correctamente.');

        $this->assertModelMissing($student);
        $this->assertSame(0, Enrollment::count());
        $this->assertSame(0, Grade::count());
        $this->assertDatabaseMissing('sessions', ['user_id' => $student->id]);

        // The course and its subjects are kept
        $this->assertModelExists($course);
        $this->assertSame(1, CourseSubject::count());
    }

    public function test_admin_cannot_delete_non_students_through_this_route(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $teacher = $this->user(Role::TEACHER, 'profe@educenter.es');

        $this->actingAs($admin)->delete(route('admin.students.destroy', $teacher))->assertNotFound();
        $this->actingAs($admin)->delete(route('admin.students.destroy', $admin))->assertNotFound();

        $this->assertModelExists($teacher);
        $this->assertModelExists($admin);
    }

    public function test_student_cannot_delete_students(): void
    {
        $student = $this->user(Role::STUDENT, 'alumno@educenter.es');
        $other = $this->user(Role::STUDENT, 'otro@educenter.es');

        $this->actingAs($student)->delete(route('admin.students.destroy', $other))->assertForbidden();

        $this->assertModelExists($other);
    }

    public function test_deleting_missing_student_returns_404(): void
    {
        $this->actingAs($this->user(Role::ADMIN, 'admin@educenter.es'))
            ->delete(route('admin.students.destroy', 999))
            ->assertNotFound();
    }
}
