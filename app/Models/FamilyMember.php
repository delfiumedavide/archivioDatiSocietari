<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'nome', 'cognome', 'codice_fiscale',
        'relazione', 'data_nascita', 'luogo_nascita',
        'data_inizio', 'data_fine', 'note',
    ];

    protected function casts(): array
    {
        return [
            'data_nascita' => 'date',
            'data_inizio' => 'date',
            'data_fine' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('data_fine');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->nome} {$this->cognome}";
    }
}
