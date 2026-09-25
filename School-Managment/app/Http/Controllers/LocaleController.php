<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Language selector in /ajustes (guests and logged-in users).
 */
class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $languages = config('app.available_locales');

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys($languages))],
        ]);

        $locale = $validated['locale'];
        app()->setLocale($locale); // the confirmation message is already in the new language

        return redirect()->to(route('settings.index').'#seccion-idioma')
            ->withCookie(cookie(SetLocale::COOKIE, $locale, SetLocale::COOKIE_MINUTES))
            ->with('success', __('Idioma cambiado a :language.', ['language' => $languages[$locale]]));
    }
}
