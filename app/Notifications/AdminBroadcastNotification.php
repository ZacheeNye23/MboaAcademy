<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminBroadcastNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info',  // info | success | warning
    ) {}

    // ── Canaux d'envoi ───────────────────────────────────────────────────────
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
        // Retirez 'mail' si vous ne voulez qu'une notif in-app
    }

    // ── Stockage en base (table notifications) ───────────────────────────────
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->type,
            'target'  => 'broadcast',
        ];
    }

    // ── Email ────────────────────────────────────────────────────────────────
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour '.$notifiable->first_name.' 👋')
            ->line($this->message);

        // Couleur du bouton selon le type
        if ($this->type === 'warning') {
            $mail->line('⚠️ Ce message nécessite votre attention.');
        }

        return $mail
            ->action('Accéder à la plateforme', url('/'))
            ->salutation('L\'équipe MboaAcademy');
    }
}