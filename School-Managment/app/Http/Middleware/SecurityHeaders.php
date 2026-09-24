<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Content Security Policy: only our own scripts run (blocks injected <script> and
     * onclick="..." attributes). Inline styles and Google Fonts are allowed because the
     * views and app.css use them.
     */
    private const CSP = "default-src 'self'; "
        ."script-src 'self'; "
        ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
        ."font-src 'self' https://fonts.gstatic.com data:; "
        ."img-src 'self' data:; "
        ."connect-src 'self'; "
        ."object-src 'none'; "
        ."base-uri 'self'; "
        ."form-action 'self'; "
        ."frame-ancestors 'none'";

    /**
     * Add security headers to every web response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        // Clickjacking: the site can't be shown inside an <iframe>
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Laravel's debug error page uses inline scripts, so skip the CSP there
        if (!(config('app.debug') && $response->isServerError())) {
            $headers->set('Content-Security-Policy', self::CSP);
        }

        // Force HTTPS in the browser once the site is served over HTTPS
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
