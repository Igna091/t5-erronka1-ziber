<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUpdateStudentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->user(Role::ADMIN, 'admin@educenter.es', null);
        $this->student = $this->user(Role::STUDENT, 'maria@educenter.es', '12345678A');
    }

    private function user(string $role, string $email, ?string $dni): User
    {
        return User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => $email,
            'dni' => $dni,
            'password' => 'Secret123',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function updateStudent(User $student, array $data = [])
    {
        return $this->actingAs($this->admin)
            ->from(route('admin.students.edit', $student))
            ->put(route('admin.students.update', $student), array_merge([
                'name' => 'María',
                'surname' => 'García López',
                'email' => ' Maria.Garcia@EduCenter.es ',
                'dni' => '12345678a',
                'phone' => '611222333',
            ], $data));
    }

    public function test_admin_can_update_student(): void
    {
        $this->updateStudent($this->student)
            ->assertRedirect(route('admin.students.show', $this->student))
            ->assertSessionHas('success');

        $this->student->refresh();
        $this->assertSame('María', $this->student->name);
        $this->assertSame('García López', $this->student->surname);
        $this->assertSame('maria.garcia@educenter.es', $this->student->email);
        $this->assertSame('12345678A', $this->student->dni);
        $this->assertSame('611222333', $this->student->phone);
    }

    public function test_student_can_keep_own_email_and_dni(): void
    {
        $this->updateStudent($this->student, ['email' => 'maria@educenter.es', 'dni' => '12345678A'])
            ->assertSessionHasNoErrors();
    }

    public function test_email_and_dni_of_another_user_are_rejected(): void
    {
        $this->user(Role::STUDENT, 'carlos@educenter.es', '23456789B');

        $this->updateStudent($this->student, ['email' => 'carlos@educenter.es'])->assertSessionHasErrors('email');
        $this->updateStudent($this->student, ['dni' => '23456789B'])->assertSessionHasErrors('dni');

        $this->assertSame('maria@educenter.es', $this->student->refresh()->email);
    }

    public function test_phone_can_be_cleared(): void
    {
        $this->student->update(['phone' => '600111222']);

        $this->updateStudent($this->student, ['phone' => ''])->assertSessionHasNoErrors();

        $this->assertNull($this->student->refresh()->phone);
    }

    public function test_role_password_and_status_cannot_be_changed(): void
    {
        $this->updateStudent($this->student, [
            'role_id' => Role::where('name', Role::ADMIN)->value('id'),
            'password' => 'Hacked123',
            'is_registered' => false,
        ]);

        $this->student->refresh();
        $this->assertTrue($this->student->isStudent());
        $this->assertTrue($this->student->is_registered);
        $this->assertTrue(Hash::check('Secret123', $this->student->password));
    }

    public function test_updated_email_is_used_to_login(): void
    {
        $this->updateStudent($this->student);
        $this->post('/logout');

        $this->post('/login', ['email' => 'maria.garcia@educenter.es', 'password' => 'Secret123'])
            ->assertRedirect(route('courses.index'));
    }

    public function test_non_students_cannot_be_updated_through_this_route(): void
    {
        $this->updateStudent($this->admin, ['dni' => '99999999Z'])->assertNotFound();

        $this->assertSame('admin@educenter.es', $this->admin->refresh()->email);
    }

    public function test_student_cannot_update_students(): void
    {
        $this->actingAs($this->student)
            ->put(route('admin.students.update', $this->student), ['name' => 'Hack'])
            ->assertForbidden();
    }
}
