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
        $when = ['date' => now()->format('d/m/Y'), 'time' => now()->format('H:i')];
        $mail = (new MailMessage)->greeting(__('¡Hola, :name!', ['name' => $this->name]));

        if ($this->change === self::PASSWORD) {
            $mail->subject(__('Tu contraseña de ZiberEibar ha cambiado'))
                ->line(__('La contraseña de tu cuenta se cambió el :date a las :time.', $when))
                ->line(__('Por seguridad, hemos cerrado tu sesión en los demás dispositivos.'));
        } else {
            $mail->subject(__('El email de tu cuenta de ZiberEibar ha cambiado'))
                ->line(__('El email con el que inicias sesión se cambió el :date a las :time.', $when))
                ->line(__('Ahora es: :email', ['email' => self::mask((string) $this->newEmail)]));
        }

        return $mail
            ->line(__('Si no has sido tú, contacta con el centro cuanto antes en :email.', ['email' => 'info@zibereibar.eus']))
            ->salutation(__('Un saludo, el equipo de ZiberEibar'));
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
