<?php

namespace App\Services;

use App\Mail\DocumentExpiryReminderMail;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ExpiryEmailService
{
    /**
     * Invia la memo scadenze a tutti gli utenti attivi (scoped per company)
     * e agli indirizzi aggiuntivi configurati (vista globale).
     *
     * @return array{sent: int, errors: list<string>}
     */
    public function sendAll(int $days, string $rawExtraEmails = ''): array
    {
        $sent   = 0;
        $errors = [];

        // ── 1. Email personalizzata a ogni utente attivo ────────────────────
        $users = User::active()
            ->whereNotNull('email')
            ->with(['roles', 'companies'])
            ->get();

        foreach ($users as $user) {
            $expiring = Document::withDetails()
                ->forUser($user)->expiring($days)->orderBy('expiration_date')->get();

            $expired = Document::withDetails()
                ->forUser($user)->expired()->orderBy('expiration_date')->get();

            if ($expiring->isEmpty() && $expired->isEmpty()) {
                continue;
            }

            try {
                Mail::to($user->email)->send(new DocumentExpiryReminderMail($expiring, $expired, $days));
                $sent++;
            } catch (\Exception $e) {
                $errors[] = $user->email;
            }
        }

        // ── 2. Indirizzi aggiuntivi configurati manualmente (vista globale) ─
        $rawExtraEmails = trim($rawExtraEmails);

        if (!empty($rawExtraEmails)) {
            $extraEmails = collect(preg_split('/[\r\n,]+/', $rawExtraEmails))
                ->map(fn ($e) => trim($e))
                ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values();

            if ($extraEmails->isNotEmpty()) {
                $allExpiring = Document::withDetails()->expiring($days)->orderBy('expiration_date')->get();
                $allExpired  = Document::withDetails()->expired()->orderBy('expiration_date')->get();

                if ($allExpiring->isNotEmpty() || $allExpired->isNotEmpty()) {
                    $mailable = new DocumentExpiryReminderMail($allExpiring, $allExpired, $days);

                    foreach ($extraEmails as $email) {
                        try {
                            Mail::to($email)->send($mailable);
                            $sent++;
                        } catch (\Exception $e) {
                            $errors[] = $email;
                        }
                    }
                }
            }
        }

        return ['sent' => $sent, 'errors' => $errors];
    }
}
