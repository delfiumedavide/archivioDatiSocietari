<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'nome',
        'cognome',
        'codice_fiscale',
        'data_nascita',
        'luogo_nascita',
        'email',
        'telefono',
        'indirizzo_residenza',
        'comune_residenza',
        'provincia_residenza',
        'cap_residenza',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'data_nascita' => 'date',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }

    public function officers(): HasMany
    {
        return $this->hasMany(CompanyOfficer::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->nome} {$this->cognome}");
    }

    public function getIdentityDocumentAttribute(): ?MemberDocument
    {
        return $this->documents->firstWhere('type', 'documento_identita');
    }

    public function getTaxCodeDocumentAttribute(): ?MemberDocument
    {
        return $this->documents->firstWhere('type', 'codice_fiscale');
    }
}
