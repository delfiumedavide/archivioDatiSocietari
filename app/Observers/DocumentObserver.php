<?php

namespace App\Observers;

use App\Models\Document;

class DocumentObserver
{
    public function updating(Document $document): void
    {
        if ($document->isDirty('expiration_date')) {
            $document->expiration_notified = false;
            $document->expiration_status = $document->computed_status;
        }
    }
}
