<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\ActivateAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminCreateTeacherTest extends TestCase
{
    use RefreshDatabase;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->course = Course::create([
            'name' => 'Desarrollo Web',
            'code' => 'DW-001',
            'academic_year' => '2026-2027',
            'status' => 'active',
        ]);
    }

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => 'Ana',
            'surname' => 'Etxeberria',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function admin(): User
    {
        return $this->user(Role::ADMIN, 'admin@educenter.es');
    }

    private function subjectIn(Course $course, string $code, ?User $teacher = null): CourseSubject
    {
        $subject = Subject::create(['code' => $code, 'name' => 'Asignatura '.$code, 'hours' => 100]);
        $course->subjects()->attach($subject, ['teacher_id' => $teacher?->id]);

        return $course->courseSubjects()->where('subject_id', $subject->id)->first();
    }

    private function storeTeacher(array $data = [])
    {
        return $this->from(route('admin.teachers.create'))->post(route('admin.teachers.store'), array_merge([
            'name' => 'Miren',
            'surname' => 'Arrieta Goiri',
            'email' => 'Miren@EduCenter.es',
            'dni' => ' 33333333p ',
            'phone' => '611111111',
            'course_id' => $this->course->id,
            'whole_course' => '1',
        ], $data));
    }

    private function teacher(): ?User
    {
        return User::firstWhere('email', 'miren@educenter.es');
    }

    public function test_sidebar_has_the_new_teacher_shortcut(): void
    {
        $this->actingAs($this->admin())->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.teachers.create'))
            ->assertSee('nuevo profesor');
    }

    public function test_form_lists_courses_and_their_subjects(): void
    {
        $other = $this->user(Role::TEACHER, 'jon@educenter.es');
        $this->subjectIn($this->course, 'PROG', $other);

        $this->actingAs($this->admin())->get(route('admin.teachers.create', ['course_id' => $this->course->id]))
            ->assertOk()
            ->assertSee('DW-001 · Desarrollo Web')
            ->assertSee('Asignatura PROG')
            ->assertSee('ahora: Ana Etxeberria')
            ->assertViewHas('selectedCourseId', $this->course->id);
    }

    public function test_admin_can_create_pending_teacher_for_the_whole_course(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->admin())->storeTeacher();

        $teacher = $this->teacher();
        $this->assertNotNull($teacher);
        $response->assertRedirect(route('admin.teachers.show', $teacher))
            ->assertSessionHas('success', 'Profesor/a creado/a. Le hemos enviado un email a miren@educenter.es para activar su cuenta.');

        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($teacher->is_registered);
        $this->assertSame('33333333P', $teacher->dni);
        $this->assertStringStartsWith('$argon2id$', $teacher->password);
        $this->assertTrue($this->course->teachers()->whereKey($teacher->id)->exists());
        Notification::assertSentTo($teacher, ActivateAccount::class);
    }

    public function test_teacher_can_be_given_subjects_of_the_course(): void
    {
        $previous = $this->user(Role::TEACHER, 'jon@educenter.es');
        $prog = $this->subjectIn($this->course, 'PROG', $previous);
        $bd = $this->subjectIn($this->course, 'BD');
        $red = $this->subjectIn($this->course, 'RED', $previous);

        $this->actingAs($this->admin())
            ->storeTeacher(['whole_course' => null, 'course_subjects' => [$prog->id, $bd->id]])
            ->assertSessionHasNoErrors();

        $teacher = $this->teacher();
        // The previous teacher of PROG is replaced; RED wasn't ticked and keeps its teacher
        $this->assertSame($teacher->id, $prog->refresh()->teacher_id);
        $this->assertSame($teacher->id, $bd->refresh()->teacher_id);
        $this->assertSame($previous->id, $red->refresh()->teacher_id);
        $this->assertFalse($this->course->teachers()->exists());
    }

    public function test_new_subject_is_created_in_the_course(): void
    {
        $this->actingAs($this->admin())->storeTeacher([
            'whole_course' => null,
            'subject_code' => ' diw ',
            'subject_name' => 'Diseño de Interfaces Web',
            'subject_hours' => '120',
        ])->assertSessionHasNoErrors();

        $subject = Subject::firstWhere('code', 'DIW');
        $this->assertNotNull($subject);
        $this->assertSame(120, $subject->hours);
        $this->assertSame($this->teacher()->id, $this->course->courseSubjects()->where('subject_id', $subject->id)->value('teacher_id'));
    }

    public function test_something_must_be_assigned(): void
    {
        $this->actingAs($this->admin())
            ->storeTeacher(['whole_course' => null])
            ->assertSessionHasErrors('assignment');

        $this->assertNull($this->teacher());
    }

    public function test_course_is_required(): void
    {
        $this->actingAs($this->admin())
            ->storeTeacher(['course_id' => ''])
            ->assertSessionHasErrors('course_id');

        $this->assertNull($this->teacher());
    }

    public function test_subjects_of_another_course_are_rejected(): void
    {
        $other = Course::create(['name' => 'Redes', 'code' => 'RD-001', 'academic_year' => '2026-2027', 'status' => 'active']);
        $foreign = $this->subjectIn($other, 'RED');

        $this->actingAs($this->admin())
            ->storeTeacher(['course_subjects' => [$foreign->id]])
            ->assertSessionHasErrors('course_subjects.0');

        $this->assertNull($this->teacher());
        $this->assertNull($foreign->refresh()->teacher_id);
    }

    public function test_new_subject_needs_all_fields_and_a_unique_code(): void
    {
        $this->actingAs($this->admin());
        $this->subjectIn($this->course, 'PROG');

        $this->storeTeacher(['subject_code' => 'DIW'])->assertSessionHasErrors(['subject_name', 'subject_hours']);
        $this->storeTeacher(['subject_code' => 'prog', 'subject_name' => 'Otra', 'subject_hours' => '50'])
            ->assertSessionHasErrors(['subject_code' => 'Ya existe una asignatura con este código.']);

        $this->assertNull($this->teacher());
        $this->assertSame(1, Subject::count());
    }

    public function test_email_and_dni_must_be_unique(): void
    {
        $student = $this->user(Role::STUDENT, 'iker@educenter.es');
        $student->update(['dni' => '11111111H']);

        $this->actingAs($this->admin());
        $this->storeTeacher(['email' => 'IKER@educenter.es'])->assertSessionHasErrors('email');
        $this->storeTeacher(['dni' => '11111111h'])->assertSessionHasErrors('dni');

        $this->assertSame(0, User::teachers()->count());
    }

    public function test_only_admins_can_create_teachers(): void
    {
        $this->storeTeacher()->assertRedirect(route('login'));

        $this->actingAs($this->user(Role::TEACHER, 'jon@educenter.es'))->storeTeacher()->assertForbidden();
        $this->actingAs($this->user(Role::STUDENT, 'iker@educenter.es'))->get(route('admin.teachers.create'))->assertForbidden();

        $this->assertNull($this->teacher());
    }

    public function test_created_teacher_can_activate_account_and_login(): void
    {
        Notification::fake();

        $this->actingAs($this->admin())->storeTeacher();
        $this->post('/logout');

        $teacher = $this->teacher();
        $url = null;
        Notification::assertSentTo($teacher, ActivateAccount::class, function (ActivateAccount $notification) use (&$url, $teacher) {
            $mail = $notification->toMail($teacher);
            $url = $mail->actionUrl;
            $this->assertTrue($mail->viewData['teacher']);

            return true;
        });

        // The activation page talks to a teacher, not a student
        $this->get($url)->assertOk()->assertSee('activación // cuenta de profesor');

        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->post(route('activation.store'), [
            'token' => basename(parse_url($url, PHP_URL_PATH)),
            'email' => $query['email'],
            'password' => 'MiClave123',
            'password_confirmation' => 'MiClave123',
        ])->assertRedirect(route('courses.index'));

        $this->post('/logout');

        $this->post('/login', ['email' => 'miren@educenter.es', 'password' => 'MiClave123'])
            ->assertRedirect(route('courses.index'));
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_teacher_page_shows_what_they_teach(): void
    {
        $this->actingAs($this->admin())->storeTeacher(['subject_code' => 'DIW', 'subject_name' => 'Diseño de Interfaces Web', 'subject_hours' => '120']);

        $this->get(route('admin.teachers.show', $this->teacher()))
            ->assertOk()
            ->assertSee('Miren Arrieta Goiri')
            ->assertSee('curso entero')
            ->assertSee('Diseño de Interfaces Web')
            ->assertSee(route('admin.teachers.resend-activation', $this->teacher()));
    }

    public function test_teacher_page_is_only_for_teachers(): void
    {
        $student = $this->user(Role::STUDENT, 'iker@educenter.es');

        $this->actingAs($this->admin())->get(route('admin.teachers.show', $student))->assertNotFound();
        $this->post(route('admin.teachers.resend-activation', $student))->assertNotFound();
    }

    public function test_admin_can_resend_the_activation_email(): void
    {
        Notification::fake();
        $this->actingAs($this->admin())->storeTeacher();
        $teacher = $this->teacher();

        // Less than a minute after the first email
        $this->from(route('admin.teachers.show', $teacher))->post(route('admin.teachers.resend-activation', $teacher))
            ->assertSessionHas('error');

        $this->travel(2)->minutes();
        $this->from(route('admin.teachers.show', $teacher))->post(route('admin.teachers.resend-activation', $teacher))
            ->assertSessionHas('success');

        Notification::assertSentToTimes($teacher, ActivateAccount::class, 2);
    }

    public function test_course_page_lists_its_teachers(): void
    {
        $jon = $this->user(Role::TEACHER, 'jon@educenter.es');
        $this->subjectIn($this->course, 'PROG', $jon);
        $this->subjectIn($this->course, 'BD');
        $this->course->teachers()->attach($jon);

        $this->actingAs($this->admin())->get(route('admin.courses.show', $this->course))
            ->assertOk()
            ->assertSee('profesorado')
            ->assertSee('Asignatura PROG')
            ->assertSee('por asignar')
            ->assertSee(route('admin.teachers.show', $jon))
            ->assertSee(route('admin.teachers.create', ['course_id' => $this->course->id]));
    }

    public function test_deleting_a_course_removes_its_teacher_assignments(): void
    {
        $this->actingAs($this->admin())->storeTeacher();

        $this->delete(route('admin.courses.destroy', $this->course))->assertSessionHas('success');

        $this->assertDatabaseCount('course_teacher', 0);
        $this->assertNotNull($this->teacher());
    }
}
