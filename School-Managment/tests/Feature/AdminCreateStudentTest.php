<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateStudentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@educenter.es',
            'password' => 'password',
            'role_id' => Role::where('name', Role::ADMIN)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function storeStudent(array $data = [])
    {
        return $this->from(route('admin.students.create'))->post(route('admin.students.store'), array_merge([
            'name' => 'Iker',
            'surname' => 'Lopez Garate',
            'email' => 'Iker@EduCenter.es',
            'dni' => ' 11111111h ',
            'phone' => '600000000',
        ], $data));
    }

    public function test_admin_can_create_pending_student(): void
    {
        $this->actingAs($this->admin());

        $response = $this->storeStudent();

        $student = User::where('email', 'iker@educenter.es')->first();
        $this->assertNotNull($student);
        $response->assertRedirect(route('admin.students.show', $student))->assertSessionHas('success');

        $this->assertTrue($student->isStudent());
        $this->assertFalse($student->is_registered);
        $this->assertSame('11111111H', $student->dni);
        $this->assertSame('600000000', $student->phone);
        $this->assertStringStartsWith('$argon2id$', $student->password);
    }

    public function test_email_and_dni_must_be_unique(): void
    {
        $this->actingAs($this->admin());
        $this->storeStudent();

        $this->storeStudent(['email' => 'iker@educenter.es', 'dni' => '22222222J'])->assertSessionHasErrors('email');
        $this->storeStudent(['email' => 'otro@educenter.es', 'dni' => '11111111H'])->assertSessionHasErrors('dni');

        $this->assertSame(1, User::students()->count());
    }

    public function test_required_fields_are_validated(): void
    {
        $this->actingAs($this->admin());

        $this->storeStudent(['name' => '', 'surname' => '', 'email' => 'no-es-email', 'dni' => ''])
            ->assertSessionHasErrors(['name', 'surname', 'email', 'dni']);

        $this->assertSame(0, User::students()->count());
    }

    public function test_non_admin_cannot_create_students(): void
    {
        $student = User::create([
            'name' => 'Maria',
            'email' => 'maria@educenter.es',
            'password' => 'password',
            'is_registered' => true,
        ]);

        $this->actingAs($student)->storeStudent()->assertForbidden();

        $this->assertNull(User::where('email', 'iker@educenter.es')->first());
    }

    public function test_created_student_can_activate_account_and_login(): void
    {
        $this->actingAs($this->admin())->storeStudent();
        $this->post('/logout');

        $this->post('/register', [
            'email' => 'iker@educenter.es',
            'dni' => '11111111H',
            'password' => 'MiClave123',
            'password_confirmation' => 'MiClave123',
        ])->assertRedirect(route('courses.index'));

        $this->post('/logout');

        $this->post('/login', ['email' => 'IKER@educenter.es', 'password' => 'MiClave123'])
            ->assertRedirect(route('courses.index'));

        $this->assertAuthenticated();
    }
}
