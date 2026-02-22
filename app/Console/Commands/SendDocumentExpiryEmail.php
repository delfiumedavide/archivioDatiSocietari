<?php

namespace App\Console\Commands;

use App\Mail\DocumentExpiryReminderMail;
use App\Models\AppSetting;
use App\Models\Document;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDocumentExpiryEmail extends Command
{
    protected $signature = 'email:send-expiry-reminder';
    protected $description = 'Invia email promemoria scadenze documenti agli utenti del sistema e agli indirizzi aggiuntivi configurati';

    public function handle(): int
    {
        $settings = AppSetting::instance();
        $days     = $settings->expiry_reminder_days ?? 30;

        $totalSent = 0;
        $errors    = [];

        // ── 1. Email personalizzata a ogni utente attivo ────────────────────
        $users = User::active()
            ->whereNotNull('email')
            ->with(['roles', 'companies'])
            ->get();

        foreach ($users as $user) {
            $expiring = Document::with(['company', 'member', 'category'])
                ->forUser($user)
                ->expiring($days)
                ->orderBy('expiration_date')
                ->get();

            $expired = Document::with(['company', 'member', 'category'])
                ->forUser($user)
                ->expired()
                ->orderBy('expiration_date')
                ->get();

            if ($expiring->isEmpty() && $expired->isEmpty()) {
                continue;
            }

            try {
                Mail::to($user->email)->send(new DocumentExpiryReminderMail($expiring, $expired, $days));
                $totalSent++;
                $this->line("  → {$user->email} ({$expiring->count()} in scadenza, {$expired->count()} scaduti)");
            } catch (\Exception $e) {
                $errors[] = $user->email;
                $this->error("  ✗ Errore per {$user->email}: {$e->getMessage()}");
            }
        }

        // ── 2. Indirizzi aggiuntivi configurati manualmente (vista globale) ─
        $rawEmails = trim($settings->notification_emails ?? '');

        if (!empty($rawEmails)) {
            $extraEmails = collect(preg_split('/[\r\n,]+/', $rawEmails))
                ->map(fn ($e) => trim($e))
                ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values();

            if ($extraEmails->isNotEmpty()) {
                $allExpiring = Document::with(['company', 'member', 'category'])
                    ->expiring($days)->orderBy('expiration_date')->get();

                $allExpired = Document::with(['company', 'member', 'category'])
                    ->expired()->orderBy('expiration_date')->get();

                if ($allExpiring->isNotEmpty() || $allExpired->isNotEmpty()) {
                    $mailable = new DocumentExpiryReminderMail($allExpiring, $allExpired, $days);

                    foreach ($extraEmails as $email) {
                        try {
                            Mail::to($email)->send($mailable);
                            $totalSent++;
                            $this->line("  → {$email} (indirizzo aggiuntivo — tutti i documenti)");
                        } catch (\Exception $e) {
                            $errors[] = $email;
                            $this->error("  ✗ Errore per {$email}: {$e->getMessage()}");
                        }
                    }
                }
            }
        }

        if ($totalSent === 0 && empty($errors)) {
            $this->info('Nessun documento in scadenza o scaduto. Nessuna email inviata.');
        } else {
            $this->info("Email promemoria inviate a {$totalSent} destinatari." . (empty($errors) ? '' : ' Errori: ' . implode(', ', $errors)));
        }

        return empty($errors) ? Command::SUCCESS : Command::FAILURE;
    }
}
