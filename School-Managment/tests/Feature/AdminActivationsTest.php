<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\ActivateAccount;
use App\Services\AccountActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminActivationsTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, bool $registered = true): User
    {
        return User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::where('name', $role)->value('id'),
            'is_registered' => $registered,
        ]);
    }

    private function admin(): User
    {
        return $this->user(Role::ADMIN, 'admin@educenter.es');
    }

    public function test_page_lists_only_pending_students_with_link_status(): void
    {
        $admin = $this->admin();
        $noLink = $this->user(Role::STUDENT, 'sinenlace@educenter.es', false);
        $withLink = $this->user(Role::STUDENT, 'conenlace@educenter.es', false);
        $expired = $this->user(Role::STUDENT, 'caducado@educenter.es', false);
        $this->user(Role::STUDENT, 'activo@educenter.es');
        $this->user(Role::TEACHER, 'profe@educenter.es', false);

        $activation = app(AccountActivation::class);
        $this->travel(-8)->days();
        $activation->createLink($expired);
        $this->travelBack();
        $activation->createLink($withLink);

        $response = $this->actingAs($admin)->get(route('admin.activations.index'))->assertOk();

        $this->assertEqualsCanonicalizing(
            [$noLink->id, $withLink->id, $expired->id],
            $response->viewData('students')->pluck('id')->all()
        );
        $response->assertSee('sin enlace vigente')
            ->assertSee('caducado')
            ->assertSee('enviado hace')
            ->assertDontSee('activo@educenter.es')
            ->assertDontSee('profe@educenter.es')
            ->assertSee('Reenviar a todos (3)');
    }

    public function test_resend_all_skips_students_emailed_less_than_a_minute_ago(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $a = $this->user(Role::STUDENT, 'a@educenter.es', false);
        $b = $this->user(Role::STUDENT, 'b@educenter.es', false);
        $recent = $this->user(Role::STUDENT, 'recent@educenter.es', false);
        $active = $this->user(Role::STUDENT, 'activo@educenter.es');
        app(AccountActivation::class)->createLink($recent);

        $this->actingAs($admin)
            ->from(route('admin.activations.index'))
            ->post(route('admin.activations.resend-all'))
            ->assertRedirect(route('admin.activations.index'))
            ->assertSessionHas('success', 'Enviados 2 emails de activación. 1 alumno omitido: se le envió uno hace menos de un minuto.');

        Notification::assertSentTo([$a, $b], ActivateAccount::class);
        Notification::assertNotSentTo([$recent, $active], ActivateAccount::class);
    }

    public function test_resend_all_with_no_pending_students(): void
    {
        Notification::fake();

        $this->actingAs($this->admin())
            ->post(route('admin.activations.resend-all'))
            ->assertSessionHas('success', 'No hay alumnos pendientes de activar su cuenta.');

        Notification::assertNothingSent();
    }

    public function test_resend_all_stops_when_the_mail_server_fails(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);
        $this->user(Role::STUDENT, 'a@educenter.es', false);
        $this->user(Role::STUDENT, 'b@educenter.es', false);

        $this->actingAs($this->admin())
            ->post(route('admin.activations.resend-all'))
            ->assertSessionHas('error');
    }

    public function test_sidebar_shows_shortcut_with_pending_count(): void
    {
        $admin = $this->admin();
        $this->user(Role::STUDENT, 'a@educenter.es', false);
        $this->user(Role::STUDENT, 'b@educenter.es', false);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('reenviar activación')
            ->assertSee('<span class="count count--warn" title="alumnos pendientes de activar">2</span>', false)
            ->assertDontSee('sidebar-link__label">ajustes', false);
    }

    public function test_only_admins_can_use_it(): void
    {
        $student = $this->user(Role::STUDENT, 'alumno@educenter.es');

        $this->actingAs($student)->get(route('admin.activations.index'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.activations.resend-all'))->assertForbidden();
    }
}
