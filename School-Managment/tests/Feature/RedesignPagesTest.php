<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Services\AccountActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedesignPagesTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, bool $registered = true, string $name = 'Test'): User
    {
        return User::create([
            'name' => $name,
            'surname' => 'User',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => $registered,
        ]);
    }

    public function test_course_page_lists_subjects_with_their_teacher(): void
    {
        $course = Course::create(['name' => 'Desarrollo Web', 'code' => 'DW-1', 'status' => 'active', 'capacity' => 10]);
        $teacher = $this->user(Role::TEACHER, 'jon@educenter.es', name: 'Jon');
        $subject = Subject::create(['code' => 'PROG', 'name' => 'Programación', 'hours' => 200]);
        $course->subjects()->attach($subject->id, ['teacher_id' => $teacher->id]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Qué vas a estudiar.')
            ->assertSee('Programación')
            ->assertSee('Jon User');
    }

    public function test_course_lists_show_free_spots_from_active_enrollments_only(): void
    {
        $course = Course::create(['name' => 'Ciberseguridad', 'code' => 'CS-1', 'status' => 'active', 'capacity' => 3]);
        Enrollment::create(['student_id' => $this->user(Role::STUDENT, 'a@educenter.es')->id, 'course_id' => $course->id, 'status' => 'active']);
        Enrollment::create(['student_id' => $this->user(Role::STUDENT, 'b@educenter.es')->id, 'course_id' => $course->id, 'status' => 'cancelled']);

        $this->get(route('courses.index'))->assertOk()->assertSee('2/3');
        $this->get(route('home'))->assertOk()->assertSee('2/3');
    }

    public function test_enrolled_student_sees_enrolled_state_in_course_browser(): void
    {
        $student = $this->user(Role::STUDENT, 'maria@educenter.es');
        $course = Course::create(['name' => 'Diseño UX/UI', 'code' => 'UX-1', 'status' => 'active', 'capacity' => 20]);
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);

        $this->actingAs($student)->get(route('courses.index'))
            ->assertOk()
            ->assertSee('matriculado/a')
            ->assertSee('diseno-ux-ui');
    }

    public function test_pending_student_page_shows_resend_button_with_cooldown(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $pending = $this->user(Role::STUDENT, 'ana@educenter.es', registered: false, name: 'Ana');
        app(AccountActivation::class)->createLink($pending);

        $this->actingAs($admin)->get(route('admin.students.show', $pending))
            ->assertOk()
            ->assertSee('activación de la cuenta')
            ->assertSee('Reenviar email de activación')
            ->assertSee('data-countdown=', false);

        // A minute later the button is available again
        $this->travel(61)->seconds();
        $this->actingAs($admin)->get(route('admin.students.show', $pending))
            ->assertOk()
            ->assertDontSee('data-countdown=', false);
    }

    public function test_activated_student_page_has_no_activation_panel(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $student = $this->user(Role::STUDENT, 'maria@educenter.es');

        $this->actingAs($admin)->get(route('admin.students.show', $student))
            ->assertOk()
            ->assertSee('cuenta activada')
            ->assertDontSee('Reenviar email de activación');
    }

    public function test_dashboard_counts_free_seats_of_active_courses(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        $open = Course::create(['name' => 'Abierto', 'code' => 'A-1', 'status' => 'active', 'capacity' => 10]);
        Course::create(['name' => 'Cerrado', 'code' => 'C-1', 'status' => 'inactive', 'capacity' => 30]);
        Enrollment::create(['student_id' => $this->user(Role::STUDENT, 'a@educenter.es')->id, 'course_id' => $open->id, 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        $stats = $response->viewData('stats');
        $this->assertSame(9, $stats['free_seats']);
        $this->assertSame(10, $stats['total_seats']);
        $response->assertSee('1 inactivo');
    }

    public function test_error_pages_are_in_spanish(): void
    {
        $this->get('/no-existe')->assertNotFound()->assertSee('Página no encontrada');

        $this->actingAs($this->user(Role::STUDENT, 'maria@educenter.es'))
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('Acceso denegado');
    }

    public function test_admin_lists_use_the_custom_pagination(): void
    {
        $admin = $this->user(Role::ADMIN, 'admin@educenter.es');
        for ($i = 0; $i < 12; $i++) {
            $this->user(Role::STUDENT, "alumno{$i}@educenter.es");
        }

        $this->actingAs($admin)->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('class="pager"', false)
            ->assertSee('1–10 de 12');
    }
}
