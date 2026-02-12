<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDocument extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'file_path',
        'file_name_original',
        'file_mime_type',
        'file_size',
        'expiration_date',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'file_size' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expiration_date, false);
    }

    public function getComputedStatusAttribute(): string
    {
        if (!$this->expiration_date) {
            return 'valid';
        }

        $days = $this->days_until_expiration;

        if ($days < 0) {
            return 'expired';
        }
        if ($days <= 30) {
            return 'expiring';
        }

        return 'valid';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->computed_status) {
            'expired' => 'red',
            'expiring' => 'yellow',
            default => 'green',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->computed_status) {
            'expired' => 'Scaduto',
            'expiring' => 'In Scadenza',
            default => 'Valido',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'documento_identita' => 'Documento identita',
            'codice_fiscale' => 'Codice fiscale',
            default => $this->type,
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}
