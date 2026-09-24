<?php

namespace App\Http\Controllers;

use App\Services\AccountActivation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthController extends Controller
{
    public const ACTIVATION_SENT_MESSAGE = 'Si existe una cuenta pendiente de activar con ese email, te hemos enviado un enlace de activación. Revisa también la carpeta de spam.';

    public const INVALID_LINK_MESSAGE = 'El enlace de activación no es válido o ha caducado. Solicita uno nuevo.';

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Max 5 failed attempts per email + IP, then locked for 1 minute
        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos. Inténtalo de nuevo en '.RateLimiter::availableIn($throttleKey).' segundos.',
            ]);
        }

        // Only activated accounts can log in
        $attempt = [
            ...$credentials,
            fn (Builder $query) => $query->where('is_registered', true),
        ];

        if (!Auth::attempt($attempt, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'email' => 'Las credenciales no son correctas o la cuenta aún no está activada.',
            ])->onlyInput('email', 'remember');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        // Redirect admin to admin panel, students and teachers to courses
        if (Auth::user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('courses.index'));
    }

    /**
     * Show the "resend activation email" form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Resend the activation email to a pending student.
     * The answer is always the same, so it doesn't reveal which emails exist.
     */
    public function register(Request $request, AccountActivation $activation)
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($student = $activation->pendingStudent($validated['email'])) {
            try {
                $activation->send($student);
            } catch (TransportExceptionInterface $e) {
                report($e);
            }
        }

        return back()->with('success', self::ACTIVATION_SENT_MESSAGE);
    }

    /**
     * Show the form to choose a password, opened from the activation email.
     */
    public function showActivate(Request $request, string $token, AccountActivation $activation)
    {
        $email = $request->string('email')->toString();
        $student = $activation->pendingStudent($email);

        if (!$student || !$activation->isValid($student, $token)) {
            return redirect()->route('register')->with('error', self::INVALID_LINK_MESSAGE);
        }

        return view('auth.activate', ['token' => $token, 'email' => $student->email, 'student' => $student]);
    }

    /**
     * Activate the account with the chosen password and log the student in.
     */
    public function activate(Request $request, AccountActivation $activation)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $student = $activation->pendingStudent($validated['email']);

        if (!$student || !$activation->isValid($student, $validated['token'])) {
            return back()->withErrors(['email' => self::INVALID_LINK_MESSAGE]);
        }

        $activation->activate($student, $validated['password']);

        Auth::login($student);
        $request->session()->regenerate();

        return redirect()->route('courses.index')
            ->with('success', 'Cuenta activada correctamente. ¡Bienvenido/a!');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
