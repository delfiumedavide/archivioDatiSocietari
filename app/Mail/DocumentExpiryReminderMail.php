<?php

namespace App\Mail;

use App\Models\AppSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Collection $expiring,
        public readonly Collection $expired,
        public readonly int $days,
    ) {}

    public function envelope(): Envelope
    {
        $appName = AppSetting::instance()->app_name ?? 'Archivio Societario';
        $subject = "Promemoria Scadenze Documenti - {$appName}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.document-expiry',
            with: [
                'appSettings' => AppSetting::instance(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
