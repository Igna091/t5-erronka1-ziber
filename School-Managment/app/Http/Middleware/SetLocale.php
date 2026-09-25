<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Language of the site (es / eu / en), chosen in /ajustes and kept in the "locale" cookie.
 * Runs before everything else (global middleware), so error pages are translated too.
 * The cookie isn't encrypted: it only holds a language code, and anything that isn't
 * one of the available languages is ignored.
 */
class SetLocale
{
    public const COOKIE = 'locale';

    /**
     * Minutes the choice is remembered (1 year).
     */
    public const COOKIE_MINUTES = 60 * 24 * 365;

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookies->get(self::COOKIE);

        if (self::isAvailable($locale)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    public static function isAvailable(mixed $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, config('app.available_locales', []));
    }
}
