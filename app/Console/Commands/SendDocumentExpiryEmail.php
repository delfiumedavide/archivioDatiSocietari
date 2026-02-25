<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\ExpiryEmailService;
use Illuminate\Console\Command;

class SendDocumentExpiryEmail extends Command
{
    protected $signature = 'email:send-expiry-reminder';
    protected $description = 'Invia email promemoria scadenze documenti agli utenti del sistema e agli indirizzi aggiuntivi configurati';

    public function handle(ExpiryEmailService $service): int
    {
        $settings = AppSetting::instance();
        $days     = $settings->expiry_reminder_days ?? 30;

        $this->info("Invio memo scadenze (anticipo: {$days} giorni)...");

        ['sent' => $sent, 'errors' => $errors] = $service->sendAll(
            $days,
            $settings->notification_emails ?? ''
        );

        if ($sent === 0 && empty($errors)) {
            $this->info('Nessun documento in scadenza o scaduto. Nessuna email inviata.');
        } else {
            $this->info("Email promemoria inviate a {$sent} destinatari." . (
                empty($errors) ? '' : ' Errori: ' . implode(', ', $errors)
            ));
        }

        return empty($errors) ? Command::SUCCESS : Command::FAILURE;
    }
}
