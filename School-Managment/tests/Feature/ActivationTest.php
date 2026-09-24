<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ActivateAccount;
use App\Services\AccountActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ActivationTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, bool $registered): User
    {
        return User::create([
            'name' => 'Ana',
            'email' => $email,
            'password' => 'OldPass123',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => $registered,
        ]);
    }

    private function pendingStudent(): User
    {
        return $this->user(Role::STUDENT, 'ana@educenter.es', false);
    }

    private function admin(): User
    {
        return $this->user(Role::ADMIN, 'admin@educenter.es', true);
    }

    /**
     * @return array{token: string, email: string, url: string}
     */
    private function link(User $student): array
    {
        $url = app(AccountActivation::class)->createLink($student);
        parse_str(parse_url($url, PHP_URL_QUERY), $query);

        return ['token' => basename(parse_url($url, PHP_URL_PATH)), 'email' => $query['email'], 'url' => $url];
    }

    private function activate(array $link, array $data = [])
    {
        return $this->from($link['url'])->post(route('activation.store'), array_merge([
            'token' => $link['token'],
            'email' => $link['email'],
            'password' => 'NuevaPass123',
            'password_confirmation' => 'NuevaPass123',
        ], $data));
    }

    // --- Activating the account ------------------------------------------------

    public function test_student_can_activate_account_with_link(): void
    {
        $student = $this->pendingStudent();
        $link = $this->link($student);

        $this->get($link['url'])->assertOk()->assertViewHas('email', 'ana@educenter.es');

        $this->activate($link)
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success');

        $student->refresh();
        $this->assertTrue($student->is_registered);
        $this->assertNotNull($student->email_verified_at);
        $this->assertStringStartsWith('$argon2id$', $student->password);
        $this->assertTrue(Hash::check('NuevaPass123', $student->password));
        $this->assertAuthenticatedAs($student);
    }

    public function test_link_can_only_be_used_once(): void
    {
        $student = $this->pendingStudent();
        $link = $this->link($student);
        $this->activate($link);
        $this->post('/logout');

        $this->activate($link, ['password' => 'Otra12345', 'password_confirmation' => 'Otra12345'])
            ->assertSessionHasErrors(['email' => AuthController::INVALID_LINK_MESSAGE]);

        $this->assertTrue(Hash::check('NuevaPass123', $student->refresh()->password));
    }

    public function test_link_expires_after_seven_days(): void
    {
        $link = $this->link($this->pendingStudent());

        $this->travel(8)->days();

        $this->get($link['url'])
            ->assertRedirect(route('register'))
            ->assertSessionHas('error', AuthController::INVALID_LINK_MESSAGE);
        $this->activate($link)->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_new_link_invalidates_the_previous_one(): void
    {
        $student = $this->pendingStudent();
        $old = $this->link($student);
        $this->link($student);

        $this->activate($old)->assertSessionHasErrors('email');
        $this->assertFalse($student->refresh()->is_registered);
    }

    public function test_wrong_token_or_email_is_rejected(): void
    {
        $student = $this->pendingStudent();
        $other = $this->user(Role::STUDENT, 'iker@educenter.es', false);
        $link = $this->link($student);

        $this->get(route('activation.show', ['token' => 'fake', 'email' => 'ana@educenter.es']))->assertRedirect(route('register'));
        $this->activate($link, ['token' => str_repeat('a', 64)])->assertSessionHasErrors('email');
        // Ana's token can't activate someone else's account
        $this->activate($link, ['email' => $other->email])->assertSessionHasErrors('email');

        $this->assertFalse($other->refresh()->is_registered);
        $this->assertGuest();
    }

    public function test_tokens_are_stored_hashed(): void
    {
        $link = $this->link($this->pendingStudent());

        $stored = DB::table('activation_tokens')->value('token');
        $this->assertNotSame($link['token'], $stored);
        $this->assertTrue(Hash::check($link['token'], $stored));
    }

    public function test_password_must_be_confirmed_and_strong(): void
    {
        $link = $this->link($this->pendingStudent());

        $this->activate($link, ['password_confirmation' => 'Distinta123'])->assertSessionHasErrors('password');
        $this->activate($link, ['password' => 'corta', 'password_confirmation' => 'corta'])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    // --- Resend page (/register) ---------------------------------------------------

    public function test_pending_student_can_request_a_new_link(): void
    {
        Notification::fake();
        $student = $this->pendingStudent();

        $this->from('/register')->post('/register', ['email' => ' ANA@educenter.es '])
            ->assertRedirect('/register')
            ->assertSessionHas('success', AuthController::ACTIVATION_SENT_MESSAGE);

        Notification::assertSentTo($student, ActivateAccount::class);
    }

    public function test_resend_page_does_not_reveal_which_emails_exist(): void
    {
        Notification::fake();
        $this->user(Role::STUDENT, 'activa@educenter.es', true);
        $this->user(Role::TEACHER, 'profe@educenter.es', false);

        foreach (['noexiste@educenter.es', 'activa@educenter.es', 'profe@educenter.es'] as $email) {
            $this->from('/register')->post('/register', ['email' => $email])
                ->assertSessionHas('success', AuthController::ACTIVATION_SENT_MESSAGE);
        }

        Notification::assertNothingSent();
    }

    public function test_only_one_email_per_minute(): void
    {
        Notification::fake();
        $student = $this->pendingStudent();

        $this->post('/register', ['email' => 'ana@educenter.es']);
        $this->post('/register', ['email' => 'ana@educenter.es']);
        Notification::assertSentToTimes($student, ActivateAccount::class, 1);

        $this->travel(61)->seconds();
        $this->post('/register', ['email' => 'ana@educenter.es']);
        Notification::assertSentToTimes($student, ActivateAccount::class, 2);
    }

    // --- Admin -------------------------------------------------------------------------

    public function test_admin_can_resend_activation_email(): void
    {
        Notification::fake();
        $student = $this->pendingStudent();

        $this->actingAs($this->admin())
            ->post(route('admin.students.resend-activation', $student))
            ->assertSessionHas('success');

        Notification::assertSentTo($student, ActivateAccount::class);

        // Right after, it's blocked for a minute
        $this->post(route('admin.students.resend-activation', $student))->assertSessionHas('error');
        Notification::assertSentToTimes($student, ActivateAccount::class, 1);
    }

    public function test_admin_cannot_resend_to_activated_or_non_students(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $active = $this->user(Role::STUDENT, 'activa@educenter.es', true);

        $this->actingAs($admin)->post(route('admin.students.resend-activation', $active))
            ->assertSessionHas('error', 'Este alumno/a ya ha activado su cuenta.');
        $this->actingAs($admin)->post(route('admin.students.resend-activation', $admin))->assertNotFound();

        Notification::assertNothingSent();
    }

    public function test_student_cannot_resend_activation_emails(): void
    {
        $student = $this->user(Role::STUDENT, 'activa@educenter.es', true);

        $this->actingAs($student)
            ->post(route('admin.students.resend-activation', $this->pendingStudent()))
            ->assertForbidden();
    }

    public function test_fixing_the_email_of_a_pending_student_sends_a_new_link(): void
    {
        Notification::fake();
        $student = $this->pendingStudent();
        $student->update(['surname' => 'López', 'dni' => '11111111H']);

        $this->actingAs($this->admin())->put(route('admin.students.update', $student), [
            'name' => 'Ana',
            'surname' => 'López',
            'email' => 'ana.lopez@educenter.es',
            'dni' => '11111111H',
        ])->assertSessionHas('success', 'Datos actualizados. Hemos enviado un nuevo email de activación a ana.lopez@educenter.es.');

        Notification::assertSentTo($student, ActivateAccount::class);
    }

    public function test_student_is_created_even_if_the_email_cannot_be_sent(): void
    {
        // Nothing listens on this port, so the SMTP connection fails
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $this->actingAs($this->admin())->post(route('admin.students.store'), [
            'name' => 'Iker',
            'surname' => 'López',
            'email' => 'iker@educenter.es',
            'dni' => '11111111H',
        ])->assertSessionHas('success', 'Alumno creado.')->assertSessionHas('error');

        $student = User::firstWhere('email', 'iker@educenter.es');
        $this->assertNotNull($student);
        // The failed link was removed, so the admin can retry straight away
        $this->assertSame(0, DB::table('activation_tokens')->count());
    }

    // --- Email content ---------------------------------------------------------------

    public function test_activation_email_is_in_spanish_and_contains_the_link(): void
    {
        $student = $this->pendingStudent();
        $url = $this->link($student)['url'];

        $mail = (new ActivateAccount($url))->toMail($student);
        app()->setLocale('es');
        $html = (string) $mail->render();

        $this->assertSame('Activa tu cuenta en EduCenter', $mail->subject);
        $this->assertStringContainsString('¡Hola, Ana!', $html);
        $this->assertStringContainsString(e($url), $html);
        $this->assertStringContainsString('copia y pega este enlace', $html);
    }
}
