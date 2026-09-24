<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = Role::STUDENT, array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Test',
            'email' => 'test@educenter.es',
            'password' => 'Secret123',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ], $attributes));
    }

    private function login(array $data = [])
    {
        return $this->from('/login')->post('/login', array_merge([
            'email' => 'test@educenter.es',
            'password' => 'Secret123',
        ], $data));
    }

    public function test_student_can_login_and_is_sent_to_courses(): void
    {
        $user = $this->makeUser();

        $this->login()->assertRedirect(route('courses.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_is_sent_to_admin_panel(): void
    {
        $this->makeUser(Role::ADMIN);

        $this->login()->assertRedirect(route('admin.dashboard'));
    }

    public function test_password_is_stored_as_argon2id(): void
    {
        $this->assertStringStartsWith('$argon2id$', $this->makeUser()->password);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->makeUser();

        $this->login(['password' => 'Wrong123'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_not_activated_account_cannot_login(): void
    {
        $this->makeUser(Role::STUDENT, ['is_registered' => false]);

        $this->login()->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_locked_after_five_failed_attempts(): void
    {
        $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->login(['password' => 'Wrong123']);
        }

        // Even the right password is blocked while locked
        $this->login()->assertSessionHasErrors('email');
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_remember_me_sets_remember_token(): void
    {
        $user = $this->makeUser();

        $this->login(['remember' => 'on']);

        $this->assertNotNull($user->refresh()->remember_token);
    }

    public function test_user_can_logout(): void
    {
        $this->actingAs($this->makeUser())
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
