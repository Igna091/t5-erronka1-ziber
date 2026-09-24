<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A whole class usually shares one IP (school network), so the strict limit
        // is per email + IP and the per-IP limit is wider.
        RateLimiter::for('register', fn (Request $request) => [
            Limit::perMinute(5)->by('register-email:'.Str::lower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute(30)->by('register-ip:'.$request->ip()),
        ]);

        // Failed logins per email are limited in AuthController; this stops one IP
        // from trying a password against many accounts (password spraying).
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(60)->by('login-ip:'.$request->ip()),
        ]);
    }
}
