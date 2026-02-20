<?php

namespace App\Console\Commands;

use App\Mail\DocumentExpiryReminderMail;
use App\Models\AppSetting;
use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDocumentExpiryEmail extends Command
{
    protected $signature = 'email:send-expiry-reminder';
    protected $description = 'Invia email promemoria scadenze documenti agli indirizzi configurati';

    public function handle(): int
    {
        $settings = AppSetting::instance();

        $rawEmails = trim($settings->notification_emails ?? '');

        if (empty($rawEmails)) {
            $this->warn('Nessun indirizzo email configurato. Configurare in Impostazioni → Email.');
            return Command::SUCCESS;
        }

        $emails = collect(preg_split('/[\r\n,]+/', $rawEmails))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values();

        if ($emails->isEmpty()) {
            $this->warn('Nessun indirizzo email valido trovato nella configurazione.');
            return Command::SUCCESS;
        }

        $days = $settings->expiry_reminder_days ?? 30;

        $expiring = Document::with(['company', 'member', 'category'])
            ->expiring($days)
            ->orderBy('expiration_date')
            ->get();

        $expired = Document::with(['company', 'member', 'category'])
            ->expired()
            ->orderBy('expiration_date')
            ->get();

        if ($expiring->isEmpty() && $expired->isEmpty()) {
            $this->info('Nessun documento in scadenza o scaduto. Nessuna email inviata.');
            return Command::SUCCESS;
        }

        $mailable = new DocumentExpiryReminderMail($expiring, $expired, $days);

        $sent = 0;
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send($mailable);
                $sent++;
                $this->line("  → Inviata a: {$email}");
            } catch (\Exception $e) {
                $this->error("  ✗ Errore per {$email}: {$e->getMessage()}");
            }
        }

        $this->info("Email promemoria inviata a {$sent} destinatari. (In scadenza: {$expiring->count()}, Scaduti: {$expired->count()})");

        return Command::SUCCESS;
    }
}
