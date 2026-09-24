<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUpdateCourseTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $this->course = Course::create([
            'name' => 'Desarrollo Web',
            'code' => 'DW-001',
            'academic_year' => '2026-2027',
            'capacity' => 25,
            'status' => 'active',
        ]);
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

    private function updateCourse(array $data = [])
    {
        return $this->actingAs($this->admin)
            ->from(route('admin.courses.edit', $this->course))
            ->put(route('admin.courses.update', $this->course), array_merge([
                'name' => 'Desarrollo Web Full Stack',
                'code' => ' dw-002 ',
                'description' => 'HTML, CSS, PHP y Laravel',
                'duration_hours' => 600,
                'capacity' => 30,
                'start_date' => '2026-10-01',
                'end_date' => '2027-06-30',
                'status' => 'inactive',
            ], $data));
    }

    public function test_admin_can_update_course(): void
    {
        $this->updateCourse()
            ->assertRedirect(route('admin.courses.show', $this->course))
            ->assertSessionHas('success');

        $this->course->refresh();
        $this->assertSame('Desarrollo Web Full Stack', $this->course->name);
        $this->assertSame('DW-002', $this->course->code);
        $this->assertSame('HTML, CSS, PHP y Laravel', $this->course->description);
        $this->assertSame(600, $this->course->duration_hours);
        $this->assertSame(30, $this->course->capacity);
        $this->assertSame('2026-10-01', $this->course->start_date->toDateString());
        $this->assertSame('2027-06-30', $this->course->end_date->toDateString());
        $this->assertSame('inactive', $this->course->status);
        $this->assertSame('2026-2027', $this->course->academic_year);
    }

    public function test_optional_fields_can_be_cleared(): void
    {
        $this->updateCourse([
            'description' => '',
            'duration_hours' => '',
            'capacity' => '',
            'start_date' => '',
            'end_date' => '',
        ])->assertSessionHasNoErrors();

        $this->course->refresh();
        $this->assertNull($this->course->description);
        $this->assertNull($this->course->capacity);
        $this->assertNull($this->course->start_date);
    }

    public function test_course_can_keep_its_own_name_and_code(): void
    {
        $this->updateCourse(['name' => 'Desarrollo Web', 'code' => 'DW-001'])->assertSessionHasNoErrors();
    }

    public function test_code_of_another_course_is_rejected(): void
    {
        Course::create(['name' => 'UX', 'code' => 'UX-001', 'academic_year' => '2026-2027', 'status' => 'active']);

        $this->updateCourse(['code' => 'ux-001'])->assertSessionHasErrors('code');
    }

    public function test_name_of_another_course_in_same_year_is_rejected(): void
    {
        Course::create(['name' => 'UX', 'code' => 'UX-001', 'academic_year' => '2026-2027', 'status' => 'active']);
        Course::create(['name' => 'Redes', 'code' => 'RD-001', 'academic_year' => '2025-2026', 'status' => 'active']);

        $this->updateCourse(['name' => 'UX'])->assertSessionHasErrors('name');
        // Same name in a different academic year is fine
        $this->updateCourse(['name' => 'Redes'])->assertSessionHasNoErrors();
    }

    public function test_academic_year_follows_start_date(): void
    {
        $this->updateCourse(['start_date' => '2027-09-15', 'end_date' => ''])->assertSessionHasNoErrors();
        $this->assertSame('2027-2028', $this->course->refresh()->academic_year);

        // Without a start date the academic year is kept
        $this->updateCourse(['start_date' => '', 'end_date' => ''])->assertSessionHasNoErrors();
        $this->assertSame('2027-2028', $this->course->refresh()->academic_year);
    }

    public function test_end_date_cannot_be_before_start_date(): void
    {
        $this->updateCourse(['start_date' => '2027-01-01', 'end_date' => '2026-12-31'])
            ->assertSessionHasErrors('end_date');
    }

    public function test_capacity_cannot_be_below_active_enrollments(): void
    {
        foreach (['active', 'active', 'active', 'cancelled'] as $i => $status) {
            Enrollment::create([
                'student_id' => $this->user(Role::STUDENT, "alumno{$i}@educenter.es")->id,
                'course_id' => $this->course->id,
                'status' => $status,
            ]);
        }

        $this->updateCourse(['capacity' => 2])->assertSessionHasErrors([
            'capacity' => 'La capacidad no puede ser menor que las 3 matrículas activas.',
        ]);

        // Cancelled enrollments don't count
        $this->updateCourse(['capacity' => 3])->assertSessionHasNoErrors();
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->updateCourse([
            'name' => '',
            'code' => '',
            'duration_hours' => 0,
            'capacity' => 0,
            'status' => 'deleted',
        ])->assertSessionHasErrors(['name', 'code', 'duration_hours', 'capacity', 'status']);

        $this->assertSame('Desarrollo Web', $this->course->refresh()->name);
    }

    public function test_student_cannot_update_courses(): void
    {
        $this->actingAs($this->user(Role::STUDENT, 'alumno@educenter.es'))
            ->put(route('admin.courses.update', $this->course), ['name' => 'Hack'])
            ->assertForbidden();
    }
}
