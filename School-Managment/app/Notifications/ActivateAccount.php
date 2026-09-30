<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\AccountActivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;

/**
 * Sent by the queue worker. The job is encrypted, so the token in the link isn't stored
 * readable in the jobs table (the activation_tokens table only keeps its hash).
 */
class ActivateAccount extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public string $email, public string $token)
    {
        // In the language of whoever triggers it, not the worker's default one
        $this->locale(app()->getLocale());
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
            ->action(__('Activar mi cuenta'), app(AccountActivation::class)->link($this->email, $this->token))
            ->line(__('El enlace caduca en 7 días y solo se puede usar una vez.'))
            ->line(__('Si no esperabas este correo, puedes ignorarlo.'))
            ->salutation(__('Un saludo, el equipo de ZiberEibar'));
    }

    /**
     * The email couldn't be delivered, even after retrying: remove the link nobody received,
     * so the admin sees it wasn't sent and can send it again straight away.
     */
    public function failed(Throwable $e): void
    {
        app(AccountActivation::class)->discard($this->email, $this->token);
    }
}
