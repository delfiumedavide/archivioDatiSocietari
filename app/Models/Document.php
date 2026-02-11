<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'document_category_id', 'title', 'description',
        'file_path', 'file_name_original', 'file_mime_type', 'file_size',
        'current_version', 'expiration_date', 'expiration_notified',
        'expiration_status', 'uploaded_by', 'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'expiration_notified' => 'boolean',
            'is_archived' => 'boolean',
            'file_size' => 'integer',
            'current_version' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version');
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

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereNotNull('expiration_date')
            ->where('expiration_date', '>', now())
            ->where('expiration_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
            ->where('expiration_date', '<', now());
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>', now()->addDays(30));
        });
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('document_category_id', $categoryId);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
