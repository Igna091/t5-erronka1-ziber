<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\AccountActivation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_pages_are_public(): void
    {
        $this->get(route('legal.privacy'))->assertOk()->assertSee('Política de privacidad.');
        $this->get(route('legal.terms'))->assertOk()->assertSee('Términos y condiciones.');
    }

    public function test_activation_page_says_that_activating_accepts_privacy_and_terms(): void
    {
        $student = User::create([
            'name' => 'Ana',
            'email' => 'ana@educenter.es',
            'password' => 'password',
            'role_id' => Role::where('name', Role::STUDENT)->value('id'),
            'is_registered' => false,
        ]);

        $this->get(app(AccountActivation::class)->createLink($student))
            ->assertOk()
            ->assertSee('Al activar tu cuenta aceptas nuestra')
            ->assertSee(route('legal.privacy'))
            ->assertSee(route('legal.terms'));
    }

    public function test_footer_links_to_legal_pages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('legal.privacy'))
            ->assertSee(route('legal.terms'));
    }
}
