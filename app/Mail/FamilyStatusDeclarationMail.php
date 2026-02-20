<?php

namespace App\Mail;

use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FamilyStatusDeclarationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Member $member,
        public readonly FamilyStatusDeclaration $declaration,
        public readonly string $emailSubject,
        public readonly string $emailBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.family-status-declaration',
            with: [
                'body' => $this->emailBody,
                'anno' => $this->declaration->anno,
                'memberName' => $this->member->full_name,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->declaration->generated_path) {
            return [];
        }

        $filename = "dichiarazione_{$this->member->cognome}_{$this->member->nome}_{$this->declaration->anno}.pdf";

        return [
            Attachment::fromStorageDisk('documents', $this->declaration->generated_path)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
