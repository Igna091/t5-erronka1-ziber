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
            ->action(__('Activar mi cuenta'), app(AccountActivation::class)->link($this->email, $this->token))
            ->markdown('emails.activate-account', ['name' => $notifiable->name, 'teacher' => $notifiable->isTeacher()]);
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
