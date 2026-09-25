<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ActivateAccount;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Activation links for students created by an admin. Tokens are handled by Laravel's
 * password broker ("activations" in config/auth.php): stored hashed, expire in 7 days
 * and can't be requested more than once a minute.
 */
class AccountActivation
{
    private function broker(): PasswordBroker
    {
        return Password::broker('activations');
    }

    /**
     * Find a student whose account is still pending activation.
     */
    public function pendingStudent(?string $email): ?User
    {
        if (!$email) {
            return null;
        }

        return User::students()
            ->where('email', strtolower(trim($email)))
            ->where('is_registered', false)
            ->first();
    }

    /**
     * Create a new activation link (the previous one stops working).
     */
    public function createLink(User $student): string
    {
        $token = $this->broker()->createToken($student);

        return route('activation.show', ['token' => $token, 'email' => $student->email]);
    }

    /**
     * Email a new activation link to the student.
     * Returns false (and sends nothing) if one was sent less than a minute ago.
     *
     * @throws TransportExceptionInterface when the email can't be sent (SMTP down, wrong credentials...)
     */
    public function send(User $student): bool
    {
        if ($this->broker()->getRepository()->recentlyCreatedToken($student)) {
            return false;
        }

        $url = $this->createLink($student);

        try {
            $student->notify(new ActivateAccount($url)); // in the language of whoever triggers it
        } catch (TransportExceptionInterface $e) {
            // Don't leave a link nobody received (and don't block the retry)
            $this->broker()->deleteToken($student);

            throw $e;
        }

        return true;
    }

    /**
     * Status of the current activation link of several students, with one query.
     * Keyed by user id: sentAt / expiresAt (null if there is no link), expired,
     * cooldown (seconds before another email can be sent, 0 = now).
     *
     * @param  Collection<int, User>  $students
     * @return array<int, array{sentAt: ?Carbon, expiresAt: ?Carbon, expired: bool, cooldown: int}>
     */
    public function statusFor(Collection $students): array
    {
        $sentAt = DB::table(config('auth.passwords.activations.table'))
            ->whereIn('email', $students->pluck('email'))
            ->pluck('created_at', 'email');

        $expire = (int) config('auth.passwords.activations.expire');
        $throttle = (int) config('auth.passwords.activations.throttle', 60);

        return $students->mapWithKeys(function (User $student) use ($sentAt, $expire, $throttle) {
            $sent = isset($sentAt[$student->email]) ? Carbon::parse($sentAt[$student->email]) : null;
            $expiresAt = $sent?->copy()->addMinutes($expire);

            return [$student->id => [
                'sentAt' => $sent,
                'expiresAt' => $expiresAt,
                'expired' => (bool) $expiresAt?->isPast(),
                'cooldown' => $sent ? max(0, (int) ceil($throttle - $sent->diffInSeconds(now(), true))) : 0,
            ]];
        })->all();
    }

    /**
     * Status of one student's activation link (see statusFor()).
     *
     * @return array{sentAt: ?Carbon, expiresAt: ?Carbon, expired: bool, cooldown: int}
     */
    public function statusOf(User $student): array
    {
        return $this->statusFor(new Collection([$student]))[$student->id];
    }

    /**
     * Check that the token belongs to the student and hasn't expired.
     */
    public function isValid(User $student, string $token): bool
    {
        return $this->broker()->tokenExists($student, $token);
    }

    /**
     * Activate the account with the chosen password. The link can't be used again.
     */
    public function activate(User $student, string $password): void
    {
        $student->forceFill([
            'password' => $password, // hashed with argon2id by the "hashed" cast
            'is_registered' => true,
            'email_verified_at' => now(), // the link proves they own the inbox
        ])->save();

        $this->broker()->deleteToken($student);
    }
}
