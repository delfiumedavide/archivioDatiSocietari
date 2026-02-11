<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentExpiringNotification extends Notification implements ShouldQueue
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
            ->subject('Documento in scadenza: ' . $this->document->title)
            ->greeting('Attenzione!')
            ->line("Il documento **{$this->document->title}** della società **{$this->document->company->denominazione}** è in scadenza.")
            ->line("Data scadenza: **{$this->document->expiration_date->format('d/m/Y')}**")
            ->line("Giorni rimanenti: **{$this->document->days_until_expiration}**")
            ->action('Visualizza Documento', route('documents.show', $this->document))
            ->line('Si prega di provvedere al rinnovo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'company' => $this->document->company->denominazione,
            'expiration_date' => $this->document->expiration_date->format('d/m/Y'),
            'days_remaining' => $this->document->days_until_expiration,
            'type' => 'expiring',
            'message' => "Il documento {$this->document->title} è in scadenza ({$this->document->days_until_expiration} giorni)",
        ];
    }
}
