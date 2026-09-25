<?php

namespace App\Http\Controllers;

use App\Notifications\AccountChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Account settings of the logged-in user (in /ajustes): login email and password.
 * Both changes ask for the current password.
 */
class AccountController extends Controller
{
    /**
     * Change the email used to log in.
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $validated = $request->validateWithBag('emailUpdate', [
            'email' => ['required', 'email', 'max:255', Rule::notIn([$user->email]), Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
        ], [
            'email.not_in' => __('Ese ya es tu email actual.'),
            'email.unique' => __('Ya existe una cuenta con ese email.'),
            'current_password.current_password' => __('La contraseña actual no es correcta.'),
        ]);

        $oldEmail = $user->email;
        $user->forceFill(['email' => $validated['email']])->save();

        // Warn the previous address, in case it wasn't the owner
        $this->notify($oldEmail, new AccountChanged(AccountChanged::EMAIL, $user->name, $user->email));

        return redirect()->route('settings.index')
            ->with('success', __('Email actualizado. A partir de ahora inicia sesión con :email.', ['email' => $user->email]));
    }

    /**
     * Change the password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => __('La contraseña actual no es correcta.'),
            'password.different' => __('La nueva contraseña tiene que ser distinta de la actual.'),
            'password.confirmed' => __('Las dos contraseñas nuevas no coinciden.'),
        ]);

        $user->forceFill([
            'password' => $validated['password'], // hashed with argon2id by the "hashed" cast
            'remember_token' => Str::random(60), // "remember me" cookies elsewhere stop working
        ])->save();

        // Log out every other session of this user; keep this one with a new id
        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();
        $request->session()->regenerate();

        $this->notify($user->email, new AccountChanged(AccountChanged::PASSWORD, $user->name));

        return redirect()->route('settings.index')
            ->with('success', __('Contraseña actualizada. Hemos cerrado tu sesión en los demás dispositivos.'));
    }

    /**
     * Send a security notice without breaking the change if the mail server fails.
     */
    private function notify(string $email, AccountChanged $notification): void
    {
        try {
            Notification::route('mail', $email)->notify($notification); // in the current language
        } catch (TransportExceptionInterface $e) {
            report($e);
        }
    }
}
