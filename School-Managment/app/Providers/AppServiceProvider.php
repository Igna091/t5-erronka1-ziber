<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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

        // The mail header shows the logo as "cid:logo": it goes inside the email, because
        // mail clients can't load images from this server (private address, self-signed HTTPS)
        Event::listen(function (MessageSending $event) {
            if (str_contains((string) $event->message->getHtmlBody(), 'cid:logo')) {
                $event->message->embedFromPath(public_path('images/logo-email.png'), 'logo');
            }
        });

        Paginator::defaultView('vendor.pagination.senal');
        Paginator::defaultSimpleView('vendor.pagination.senal');

        // Counters in the admin sidebar
        View::composer('layouts.admin', function ($view) {
            $view->with('adminCounts', [
                'students' => User::students()->count(),
                'pending' => User::students()->where('is_registered', false)->count(),
                'courses' => Course::count(),
                'enrollments' => Enrollment::where('status', 'active')->count(),
            ]);
        });
    }
}
