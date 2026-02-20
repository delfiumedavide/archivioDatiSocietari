<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyStatusDeclaration extends Model
{
    protected $fillable = [
        'member_id', 'anno', 'stato_civile',
        'generated_path', 'signed_path',
        'generated_at', 'signed_at',
        'note', 'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'anno' => 'integer',
            'generated_at' => 'datetime',
            'signed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('anno', $year);
    }

    public function scopeUnsigned($query)
    {
        return $query->whereNull('signed_at');
    }

    public function scopeSigned($query)
    {
        return $query->whereNotNull('signed_at');
    }

    public function getIsGeneratedAttribute(): bool
    {
        return $this->generated_at !== null;
    }

    public function getIsSignedAttribute(): bool
    {
        return $this->signed_at !== null;
    }
}
