<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_is_in_spanish_by_default(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="es"', false)
            ->assertSee('iniciar sesión');
    }

    public function test_choosing_a_language_saves_it_in_a_cookie(): void
    {
        $this->post(route('settings.locale'), ['locale' => 'eu'])
            ->assertRedirect(route('settings.index').'#seccion-idioma')
            ->assertCookie(SetLocale::COOKIE, 'eu', false)
            ->assertSessionHas('success', 'Hizkuntza aldatu da: Euskara.');
    }

    public function test_unknown_language_is_rejected(): void
    {
        $this->post(route('settings.locale'), ['locale' => 'fr'])
            ->assertSessionHasErrors('locale')
            ->assertCookieMissing(SetLocale::COOKIE);
    }

    public function test_pages_are_shown_in_basque(): void
    {
        $this->withUnencryptedCookie(SetLocale::COOKIE, 'eu')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="eu"', false)
            ->assertSee('hasi saioa')
            ->assertDontSee('iniciar sesión');

        $this->withUnencryptedCookie(SetLocale::COOKIE, 'eu')
            ->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('Pribatutasun-politika.');
    }

    public function test_pages_are_shown_in_english(): void
    {
        $this->withUnencryptedCookie(SetLocale::COOKIE, 'en')
            ->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('keep me logged in');

        $this->withUnencryptedCookie(SetLocale::COOKIE, 'en')
            ->get(route('legal.terms'))
            ->assertOk()
            ->assertSee('Terms and conditions.');
    }

    public function test_invalid_cookie_falls_back_to_spanish(): void
    {
        $this->withUnencryptedCookie(SetLocale::COOKIE, 'xx')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="es"', false);
    }

    public function test_settings_page_marks_the_current_language(): void
    {
        $this->withUnencryptedCookie(SetLocale::COOKIE, 'en')
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('<option value="en" lang="en" selected', false);
    }

    public function test_validation_messages_are_translated(): void
    {
        $this->withUnencryptedCookie(SetLocale::COOKIE, 'eu')
            ->post(route('login'), [])
            ->assertSessionHasErrors(['email' => 'emaila eremua derrigorrezkoa da.']);
    }

    public function test_every_language_file_has_the_same_texts(): void
    {
        $english = json_decode(file_get_contents(lang_path('en.json')), true);
        $basque = json_decode(file_get_contents(lang_path('eu.json')), true);
        $spanish = json_decode(file_get_contents(lang_path('es.json')), true);

        $this->assertNotEmpty($english);
        $this->assertSame([], array_diff(array_keys($english), array_keys($basque)), 'Texts missing in eu.json');

        // trans_choice() uses the fallback language (en) when the key isn't in es.json
        $plurals = array_filter(array_keys($english), fn ($key) => str_contains($key, '|'));
        $this->assertSame([], array_diff($plurals, array_keys($spanish)), 'Plural texts missing in es.json');

        foreach ([$english, $basque] as $texts) {
            foreach ($texts as $key => $text) {
                // Same :placeholders and the same number of plural forms as the Spanish text
                preg_match_all('/:[a-z]+/', $key, $expected);
                preg_match_all('/:[a-z]+/', $text, $actual);
                sort($expected[0]);
                sort($actual[0]);
                $this->assertSame($expected[0], $actual[0], "Placeholders differ in: {$text}");
                $this->assertSame(substr_count($key, '|'), substr_count($text, '|'), "Plural forms differ in: {$text}");
            }
        }
    }
}
