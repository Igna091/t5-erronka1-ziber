<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function pendingStudent(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Ana',
            'surname' => 'Fernández',
            'email' => 'ana@educenter.es',
            'password' => 'temporary',
            'dni' => '34567890C',
            'is_registered' => false,
        ], $attributes));
    }

    private function register(array $data = [])
    {
        return $this->from('/register')->post('/register', array_merge([
            'email' => 'ana@educenter.es',
            'dni' => '34567890C',
            'password' => 'NuevaPass123',
            'password_confirmation' => 'NuevaPass123',
        ], $data));
    }

    public function test_pending_student_can_activate_account(): void
    {
        $student = $this->pendingStudent();

        $this->register()->assertRedirect(route('courses.index'));

        $student->refresh();
        $this->assertTrue($student->is_registered);
        $this->assertStringStartsWith('$argon2id$', $student->password);
        $this->assertTrue(Hash::check('NuevaPass123', $student->password));
        $this->assertAuthenticatedAs($student);
    }

    public function test_dni_is_case_and_space_insensitive(): void
    {
        $this->pendingStudent();

        $this->register(['dni' => ' 34567890c '])->assertRedirect(route('courses.index'));

        $this->assertAuthenticated();
    }

    public function test_wrong_dni_is_rejected(): void
    {
        $student = $this->pendingStudent();

        $this->register(['dni' => '00000000X'])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertFalse($student->refresh()->is_registered);
        $this->assertGuest();
    }

    public function test_already_registered_account_cannot_register_again(): void
    {
        $student = $this->pendingStudent(['is_registered' => true, 'password' => 'OldPass123']);

        $this->register()->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('OldPass123', $student->refresh()->password));
        $this->assertGuest();
    }

    public function test_non_student_cannot_register(): void
    {
        $this->pendingStudent(['role_id' => Role::where('name', Role::TEACHER)->value('id')]);

        $this->register()->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_must_be_confirmed_and_strong(): void
    {
        $this->pendingStudent();

        $this->register(['password_confirmation' => 'Distinta123'])->assertSessionHasErrors('password');
        $this->register(['password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_register_is_rate_limited(): void
    {
        $this->pendingStudent();

        for ($i = 0; $i < 5; $i++) {
            $this->register(['dni' => '00000000X']);
        }

        $this->register()->assertStatus(429);
    }
}
