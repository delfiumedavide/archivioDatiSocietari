<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Document $document
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('SCADUTO: ' . $this->document->title)
            ->greeting('Urgente!')
            ->line("Il documento **{$this->document->title}** di **{$this->document->owner_name}** è **SCADUTO**.")
            ->line("Data scadenza: **{$this->document->expiration_date->format('d/m/Y')}**")
            ->action('Visualizza Documento', route('documents.show', $this->document))
            ->line('Si prega di provvedere immediatamente al rinnovo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'company' => $this->document->owner_name,
            'expiration_date' => $this->document->expiration_date->format('d/m/Y'),
            'type' => 'expired',
            'message' => "Il documento {$this->document->title} è SCADUTO",
        ];
    }
}
