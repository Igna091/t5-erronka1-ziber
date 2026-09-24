<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateCourseTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN, 'admin@educenter.es');
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

    private function storeCourse(array $data = [])
    {
        return $this->actingAs($this->admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), array_merge([
                'name' => 'Ciberseguridad',
                'code' => ' cs-001 ',
                'description' => 'Pentesting y criptografía',
                'duration_hours' => 400,
                'capacity' => 15,
                'start_date' => '2027-01-15',
                'end_date' => '2027-06-15',
                'status' => 'active',
            ], $data));
    }

    public function test_admin_can_create_course(): void
    {
        $response = $this->storeCourse();

        $course = Course::firstWhere('code', 'CS-001');
        $this->assertNotNull($course);
        $response->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('success', 'Curso «Ciberseguridad» creado correctamente.');

        $this->assertSame('Ciberseguridad', $course->name);
        $this->assertSame('Pentesting y criptografía', $course->description);
        $this->assertSame(400, $course->duration_hours);
        $this->assertSame(15, $course->capacity);
        $this->assertSame('2027-01-15', $course->start_date->toDateString());
        $this->assertSame('active', $course->status);
    }

    public function test_academic_year_is_derived_from_start_date(): void
    {
        $this->storeCourse(['code' => 'A', 'start_date' => '2026-09-01', 'end_date' => '']);
        $this->storeCourse(['code' => 'B', 'name' => 'Otro', 'start_date' => '2027-08-31', 'end_date' => '']);
        $this->storeCourse(['code' => 'C', 'name' => 'Sin fecha', 'start_date' => '', 'end_date' => '']);

        $this->assertSame('2026-2027', Course::firstWhere('code', 'A')->academic_year);
        $this->assertSame('2026-2027', Course::firstWhere('code', 'B')->academic_year);
        $this->assertNull(Course::firstWhere('code', 'C')->academic_year);
    }

    public function test_only_required_fields(): void
    {
        $this->storeCourse([
            'description' => '',
            'duration_hours' => '',
            'capacity' => '',
            'start_date' => '',
            'end_date' => '',
        ])->assertSessionHasNoErrors();

        $this->assertNull(Course::firstWhere('code', 'CS-001')->capacity);
    }

    public function test_duplicate_code_is_rejected(): void
    {
        $this->storeCourse();

        $this->storeCourse(['name' => 'Otro curso', 'code' => 'CS-001'])
            ->assertSessionHasErrors(['code' => 'Ya existe un curso con este código.']);

        $this->assertSame(1, Course::count());
    }

    public function test_same_name_only_allowed_in_another_academic_year(): void
    {
        $this->storeCourse();

        // 2027-03-01 is still in 2026-2027
        $this->storeCourse(['code' => 'CS-002', 'start_date' => '2027-03-01', 'end_date' => ''])
            ->assertSessionHasErrors(['name' => 'Ya existe un curso con este nombre en el mismo año académico.']);

        // Next academic year is fine
        $this->storeCourse(['code' => 'CS-003', 'start_date' => '2027-10-01', 'end_date' => ''])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Course::count());
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->storeCourse([
            'name' => '',
            'code' => '',
            'capacity' => 0,
            'duration_hours' => -5,
            'start_date' => 'mañana',
            'end_date' => '2020-01-01',
            'status' => 'borrador',
        ])->assertSessionHasErrors(['name', 'code', 'capacity', 'duration_hours', 'start_date', 'status']);

        $this->assertSame(0, Course::count());
    }

    public function test_end_date_cannot_be_before_start_date(): void
    {
        $this->storeCourse(['end_date' => '2027-01-01'])->assertSessionHasErrors('end_date');
    }

    public function test_old_input_is_kept_on_error(): void
    {
        $this->storeCourse(['code' => ''])->assertSessionHasInput('name', 'Ciberseguridad');
    }

    public function test_student_cannot_create_courses(): void
    {
        $this->actingAs($this->user(Role::STUDENT, 'alumno@educenter.es'))
            ->post(route('admin.courses.store'), ['name' => 'Hack', 'code' => 'H', 'status' => 'active'])
            ->assertForbidden();

        $this->assertSame(0, Course::count());
    }
}
