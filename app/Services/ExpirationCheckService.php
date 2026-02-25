<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Collection;

class ExpirationCheckService
{
    public function checkAll(): object
    {
        $newlyExpiring = collect();
        $newlyExpired  = collect();
        $validCount    = 0;
        $expiringCount = 0;
        $expiredCount  = 0;

        // chunk(500) evita di caricare l'intera tabella documenti in memoria
        Document::whereNotNull('expiration_date')->chunk(500, function ($documents) use (
            &$newlyExpiring, &$newlyExpired,
            &$validCount, &$expiringCount, &$expiredCount
        ) {
            foreach ($documents as $document) {
                $oldStatus = $document->expiration_status;
                $newStatus = $document->computed_status;

                if ($oldStatus !== $newStatus) {
                    $document->update(['expiration_status' => $newStatus]);

                    if ($newStatus === 'expiring' && !$document->expiration_notified) {
                        $newlyExpiring->push($document);
                    }

                    if ($newStatus === 'expired' && !$document->expiration_notified) {
                        $newlyExpired->push($document);
                    }
                }

                match ($newStatus) {
                    'valid'    => $validCount++,
                    'expiring' => $expiringCount++,
                    'expired'  => $expiredCount++,
                };
            }
        });

        return (object) [
            'validCount'        => $validCount,
            'expiringCount'     => $expiringCount,
            'expiredCount'      => $expiredCount,
            'newlyExpiring'     => $newlyExpiring,
            'newlyExpired'      => $newlyExpired,
            'notificationsSent' => $newlyExpiring->count() + $newlyExpired->count(),
        ];
    }

    public function getExpiringDocuments(int $days = 30): Collection
    {
        return Document::with(['company', 'category'])
            ->expiring($days)
            ->orderBy('expiration_date')
            ->get();
    }

    public function getExpiredDocuments(): Collection
    {
        return Document::with(['company', 'category'])
            ->expired()
            ->orderBy('expiration_date')
            ->get();
    }

    public function markNotified(Document $document): void
    {
        $document->update(['expiration_notified' => true]);
    }

    public function resetNotificationFlag(Document $document): void
    {
        $document->update(['expiration_notified' => false]);
    }
}
