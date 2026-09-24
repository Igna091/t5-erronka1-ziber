<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivateAccount extends Notification
{
    public function __construct(public string $url)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Activa tu cuenta en EduCenter')
            ->greeting("¡Hola, {$notifiable->name}!")
            ->line('El centro te ha dado de alta como alumno/a en EduCenter.')
            ->line('Para empezar, activa tu cuenta y elige tu contraseña:')
            ->action('Activar mi cuenta', $this->url)
            ->line('El enlace caduca en 7 días y solo se puede usar una vez.')
            ->line('Si no esperabas este correo, puedes ignorarlo.')
            ->salutation('Un saludo, el equipo de EduCenter');
    }
}
