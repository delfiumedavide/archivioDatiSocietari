<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome', 'cognome', 'codice_fiscale',
        'data_nascita', 'luogo_nascita_comune', 'luogo_nascita_provincia',
        'nazionalita', 'sesso', 'stato_civile',
        'indirizzo_residenza', 'citta_residenza', 'provincia_residenza', 'cap_residenza',
        'indirizzo_domicilio', 'citta_domicilio', 'provincia_domicilio', 'cap_domicilio',
        'telefono', 'cellulare', 'email', 'pec',
        'white_list', 'white_list_scadenza',
        'note', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'data_nascita' => 'date',
            'white_list' => 'boolean',
            'white_list_scadenza' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function officers(): HasMany
    {
        return $this->hasMany(CompanyOfficer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function familyStatusChanges(): HasMany
    {
        return $this->hasMany(FamilyStatusChange::class)->orderByDesc('data_variazione');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(FamilyStatusDeclaration::class)->orderByDesc('anno');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_officers')
            ->withPivot('ruolo', 'data_nomina', 'data_scadenza', 'data_cessazione', 'compenso')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('cognome', 'LIKE', "%{$term}%")
              ->orWhere('nome', 'LIKE', "%{$term}%")
              ->orWhere('codice_fiscale', 'LIKE', "%{$term}%");
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->nome} {$this->cognome}";
    }

    public function getCurrentStatoCivileAttribute(): ?string
    {
        $latest = $this->familyStatusChanges()->first();

        return $latest?->stato_civile ?? $this->stato_civile;
    }

    public function getIsWhiteListExpiredAttribute(): bool
    {
        return $this->white_list
            && $this->white_list_scadenza
            && $this->white_list_scadenza->isPast();
    }

    public function getIsWhiteListExpiringAttribute(): bool
    {
        return $this->white_list
            && $this->white_list_scadenza
            && $this->white_list_scadenza->isFuture()
            && $this->white_list_scadenza->diffInDays(now()) <= 30;
    }

    public function getActiveOfficerPositionsCountAttribute(): int
    {
        return $this->officers()->active()->count();
    }
}
