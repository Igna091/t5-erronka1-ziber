<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Security notice after the password or the login email of an account changes,
 * so the owner finds out if it wasn't them.
 */
class AccountChanged extends Notification
{
    public const PASSWORD = 'password';

    public const EMAIL = 'email';

    public function __construct(
        public string $change,
        public string $name,
        public ?string $newEmail = null,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $when = now()->format('d/m/Y \a \l\a\s H:i');
        $mail = (new MailMessage)->greeting("¡Hola, {$this->name}!");

        if ($this->change === self::PASSWORD) {
            $mail->subject('Tu contraseña de ZiberEibar ha cambiado')
                ->line("La contraseña de tu cuenta se cambió el {$when}.")
                ->line('Por seguridad, hemos cerrado tu sesión en los demás dispositivos.');
        } else {
            $mail->subject('El email de tu cuenta de ZiberEibar ha cambiado')
                ->line("El email con el que inicias sesión se cambió el {$when}.")
                ->line('Ahora es: '.self::mask((string) $this->newEmail));
        }

        return $mail
            ->line('Si no has sido tú, contacta con el centro cuanto antes en info@zibereibar.eus.')
            ->salutation('Un saludo, el equipo de ZiberEibar');
    }

    /**
     * "maria.garcia@educenter.es" -> "m***@educenter.es"
     */
    public static function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1).'***@'.$domain;
    }
}
