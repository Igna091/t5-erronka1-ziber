<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect admin to admin panel, students to courses
            if ($user->isAdmin()) {
                return redirect()->intended('/administracion');
            }

            return redirect()->intended('/cursos');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son correctas.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     * Only students previously created by an admin can register.
     */
    public function register(Request $request)
    {
        $request->merge(['dni' => strtoupper(trim((string) $request->input('dni')))]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'dni' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::students()
            ->where('email', $validated['email'])
            ->where('dni', $validated['dni'])
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No existe ningún alumno con ese email y DNI. Contacta con administración.',
            ])->onlyInput('email', 'dni');
        }

        if ($user->is_registered) {
            return back()->withErrors([
                'email' => 'Esta cuenta ya está activada. Inicia sesión.',
            ])->onlyInput('email', 'dni');
        }

        // Hashed with the configured driver (argon2id, see config/hashing.php)
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'is_registered' => true,
        ])->save();

        Auth::login($user);
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
