<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\AccountChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'OldPass123';

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    private function user(string $email = 'ana@educenter.es', string $role = Role::STUDENT): User
    {
        return User::create([
            'name' => 'Ana',
            'email' => $email,
            'password' => self::PASSWORD,
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => true,
        ]);
    }

    private function changePassword(User $user, array $data = [])
    {
        return $this->actingAs($user)->from(route('settings.index'))->put(route('settings.password'), array_merge([
            'current_password' => self::PASSWORD,
            'password' => 'NuevaPass456',
            'password_confirmation' => 'NuevaPass456',
        ], $data));
    }

    private function changeEmail(User $user, array $data = [])
    {
        return $this->actingAs($user)->from(route('settings.index'))->put(route('settings.email'), array_merge([
            'email' => 'ana.nueva@educenter.es',
            'current_password' => self::PASSWORD,
        ], $data));
    }

    private function sessionRow(string $id, User $user): array
    {
        return ['id' => $id, 'user_id' => $user->id, 'ip_address' => '10.0.0.1', 'user_agent' => 'test', 'payload' => '', 'last_activity' => time()];
    }

    // ---------------------------------------------------------------- page

    public function test_settings_page_shows_the_account_forms_only_when_logged_in(): void
    {
        $this->get(route('settings.index'))
            ->assertOk()
            ->assertDontSee('Cuenta y seguridad')
            ->assertDontSee(route('settings.password'));

        $this->actingAs($this->user())->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Cuenta y seguridad')
            ->assertSee('ana@educenter.es')
            ->assertSee(route('settings.email'))
            ->assertSee(route('settings.password'))
            ->assertSee('name="password_confirmation"', false);
    }

    public function test_admins_can_use_the_account_forms_too(): void
    {
        $admin = $this->user('admin@educenter.es', Role::ADMIN);

        $this->changePassword($admin)->assertRedirect(route('settings.index'))->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NuevaPass456', $admin->fresh()->password));
    }

    public function test_guests_cannot_change_an_account(): void
    {
        $this->put(route('settings.password'), ['current_password' => 'x', 'password' => 'NuevaPass456', 'password_confirmation' => 'NuevaPass456'])
            ->assertRedirect(route('login'));
        $this->put(route('settings.email'), ['email' => 'x@educenter.es', 'current_password' => 'x'])
            ->assertRedirect(route('login'));

        Notification::assertNothingSent();
    }

    // ------------------------------------------------------------ password

    public function test_user_can_change_their_password(): void
    {
        $user = $this->user();
        $oldRememberToken = $user->remember_token;

        $this->changePassword($user)
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertStringStartsWith('$argon2id$', $user->password);
        $this->assertTrue(Hash::check('NuevaPass456', $user->password));
        $this->assertFalse(Hash::check(self::PASSWORD, $user->password));
        $this->assertNotEquals($oldRememberToken, $user->remember_token);
        $this->assertAuthenticatedAs($user); // this session stays logged in

        Notification::assertSentOnDemand(AccountChanged::class, function ($notification, $channels, $notifiable) {
            return $notification->change === AccountChanged::PASSWORD
                && $notifiable->routes['mail'] === 'ana@educenter.es';
        });
    }

    public function test_user_can_log_in_with_the_new_password_only(): void
    {
        $user = $this->user();
        $this->changePassword($user);
        $this->post(route('logout'));

        $this->post('/login', ['email' => 'ana@educenter.es', 'password' => self::PASSWORD]);
        $this->assertGuest();

        $this->post('/login', ['email' => 'ana@educenter.es', 'password' => 'NuevaPass456']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_changing_the_password_logs_out_the_other_sessions_of_that_user(): void
    {
        $user = $this->user();
        $other = $this->user('otro@educenter.es');
        DB::table('sessions')->insert([
            $this->sessionRow('ana-laptop', $user),
            $this->sessionRow('ana-mobile', $user),
            $this->sessionRow('otro-pc', $other),
        ]);

        $this->changePassword($user)->assertSessionHasNoErrors();

        $this->assertSame(0, DB::table('sessions')->where('user_id', $user->id)->count());
        $this->assertTrue(DB::table('sessions')->where('id', 'otro-pc')->exists());
    }

    public function test_wrong_current_password_changes_nothing(): void
    {
        $user = $this->user();
        DB::table('sessions')->insert($this->sessionRow('ana-laptop', $user));

        $this->changePassword($user, ['current_password' => 'NoEsLaMia123'])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrorsIn('passwordUpdate', ['current_password' => 'La contraseña actual no es correcta.']);

        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
        $this->assertTrue(DB::table('sessions')->where('id', 'ana-laptop')->exists());
        Notification::assertNothingSent();
    }

    public function test_both_new_passwords_must_match(): void
    {
        $user = $this->user();

        $this->changePassword($user, ['password_confirmation' => 'OtraPass789'])
            ->assertSessionHasErrorsIn('passwordUpdate', ['password' => 'Las dos contraseñas nuevas no coinciden.']);

        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
    }

    public function test_new_password_must_be_different_from_the_current_one(): void
    {
        $user = $this->user();

        $this->changePassword($user, ['password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD])
            ->assertSessionHasErrorsIn('passwordUpdate', ['password' => 'La nueva contraseña tiene que ser distinta de la actual.']);
    }

    public function test_new_password_must_follow_the_rules(): void
    {
        $user = $this->user();

        foreach (['Ab1', 'SoloLetras', '1234567890', ''] as $weak) {
            $this->changePassword($user, ['password' => $weak, 'password_confirmation' => $weak])
                ->assertSessionHasErrorsIn('passwordUpdate', 'password');
        }

        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
        Notification::assertNothingSent();
    }

    // --------------------------------------------------------------- email

    public function test_user_can_change_their_email(): void
    {
        $user = $this->user();

        $this->changeEmail($user, ['email' => '  Ana.Nueva@EduCenter.es '])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame('ana.nueva@educenter.es', $user->fresh()->email);

        // The warning goes to the OLD address, in case someone else made the change
        Notification::assertSentOnDemand(AccountChanged::class, function ($notification, $channels, $notifiable) {
            return $notification->change === AccountChanged::EMAIL
                && $notification->newEmail === 'ana.nueva@educenter.es'
                && $notifiable->routes['mail'] === 'ana@educenter.es';
        });

        $this->post(route('logout'));
        $this->post('/login', ['email' => 'ana.nueva@educenter.es', 'password' => self::PASSWORD]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_change_needs_the_current_password(): void
    {
        $user = $this->user();

        $this->changeEmail($user, ['current_password' => 'NoEsLaMia123'])
            ->assertSessionHasErrorsIn('emailUpdate', ['current_password' => 'La contraseña actual no es correcta.']);
        $this->changeEmail($user, ['current_password' => ''])
            ->assertSessionHasErrorsIn('emailUpdate', 'current_password');

        $this->assertSame('ana@educenter.es', $user->fresh()->email);
        Notification::assertNothingSent();
    }

    public function test_email_must_be_valid_new_and_not_taken(): void
    {
        $user = $this->user();
        $this->user('ocupado@educenter.es');

        $this->changeEmail($user, ['email' => 'Ocupado@educenter.es'])
            ->assertSessionHasErrorsIn('emailUpdate', ['email' => 'Ya existe una cuenta con ese email.']);
        $this->changeEmail($user, ['email' => 'ANA@educenter.es'])
            ->assertSessionHasErrorsIn('emailUpdate', ['email' => 'Ese ya es tu email actual.']);
        $this->changeEmail($user, ['email' => 'no-es-un-email'])
            ->assertSessionHasErrorsIn('emailUpdate', 'email');
        $this->changeEmail($user, ['email' => str_repeat('a', 250).'@educenter.es'])
            ->assertSessionHasErrorsIn('emailUpdate', 'email');

        $this->assertSame('ana@educenter.es', $user->fresh()->email);
    }

    public function test_email_notice_masks_the_new_address(): void
    {
        $mail = (new AccountChanged(AccountChanged::EMAIL, 'Ana', 'ana.nueva@educenter.es'))
            ->toMail(new AnonymousNotifiable);
        $text = implode(' ', $mail->introLines);

        $this->assertSame('El email de tu cuenta de ZiberEibar ha cambiado', $mail->subject);
        $this->assertStringContainsString('a***@educenter.es', $text);
        $this->assertStringNotContainsString('ana.nueva', $text);
    }

    // -------------------------------------------------------------- safety

    public function test_a_mail_server_failure_does_not_undo_the_change(): void
    {
        $this->mock(ChannelManager::class, function ($mock) {
            $mock->shouldReceive('send')->once()->andThrow(new TransportException('SMTP caído'));
        });
        $user = $this->user();

        $this->changePassword($user)->assertRedirect(route('settings.index'))->assertSessionHas('success');

        $this->assertTrue(Hash::check('NuevaPass456', $user->fresh()->password));
    }

    public function test_guessing_the_current_password_is_throttled(): void
    {
        $user = $this->user();

        for ($i = 0; $i < 6; $i++) {
            $this->changePassword($user, ['current_password' => "Intento{$i}abc"])->assertStatus(302);
        }

        $this->changePassword($user)->assertStatus(429);
        $this->changeEmail($user)->assertStatus(429); // both forms share the limit
        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
    }
}
