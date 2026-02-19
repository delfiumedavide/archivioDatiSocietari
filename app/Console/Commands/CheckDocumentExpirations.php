<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DocumentExpiringNotification;
use App\Notifications\DocumentExpiredNotification;
use App\Services\ExpirationCheckService;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckDocumentExpirations extends Command
{
    protected $signature = 'documents:check-expirations';
    protected $description = 'Controlla le scadenze dei documenti e invia notifiche';

    public function handle(ExpirationCheckService $service, PushNotificationService $pushService): int
    {
        $this->info('Controllo scadenze documenti...');

        $report = $service->checkAll();

        $this->table(
            ['Stato', 'Conteggio'],
            [
                ['Validi', $report->validCount],
                ['In Scadenza (<=30 gg)', $report->expiringCount],
                ['Scaduti', $report->expiredCount],
            ]
        );

        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();

        foreach ($report->newlyExpiring as $document) {
            Notification::send($admins, new DocumentExpiringNotification($document));

            $pushService->sendToAdmins(
                'Documento in scadenza',
                "{$document->title} - {$document->owner_name} scade il {$document->expiration_date->format('d/m/Y')}",
                route('documents.show', $document)
            );

            $service->markNotified($document);
        }

        foreach ($report->newlyExpired as $document) {
            Notification::send($admins, new DocumentExpiredNotification($document));

            $pushService->sendToAdmins(
                'Documento scaduto!',
                "{$document->title} - {$document->owner_name} è scaduto il {$document->expiration_date->format('d/m/Y')}",
                route('documents.show', $document)
            );

            $service->markNotified($document);
        }

        $this->info("Notifiche inviate: {$report->notificationsSent}");

        return Command::SUCCESS;
    }
}
