<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyOfficer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'member_id', 'ruolo',
        'data_nomina', 'data_scadenza', 'data_cessazione',
        'compenso', 'poteri', 'note',
    ];

    protected function casts(): array
    {
        return [
            'data_nomina' => 'date',
            'data_scadenza' => 'date',
            'data_cessazione' => 'date',
            'compenso' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('data_cessazione');
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereNull('data_cessazione')
            ->whereNotNull('data_scadenza')
            ->where('data_scadenza', '<=', now()->addDays($days))
            ->where('data_scadenza', '>=', now());
    }

    public function getFullNameAttribute(): string
    {
        return $this->member?->full_name ?? '';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->data_scadenza && $this->data_scadenza->isPast();
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->data_scadenza
            && $this->data_scadenza->isFuture()
            && $this->data_scadenza->diffInDays(now()) <= 30;
    }
}
