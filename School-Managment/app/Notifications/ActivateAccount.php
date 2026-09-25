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
            ->subject(__('Activa tu cuenta en ZiberEibar'))
            ->greeting(__('¡Hola, :name!', ['name' => $notifiable->name]))
            ->line(__('El centro te ha dado de alta como alumno/a en ZiberEibar.'))
            ->line(__('Para empezar, activa tu cuenta y elige tu contraseña:'))
            ->action(__('Activar mi cuenta'), $this->url)
            ->line(__('El enlace caduca en 7 días y solo se puede usar una vez.'))
            ->line(__('Si no esperabas este correo, puedes ignorarlo.'))
            ->salutation(__('Un saludo, el equipo de ZiberEibar'));
    }
}
