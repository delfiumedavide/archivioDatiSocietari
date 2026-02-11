<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'denominazione', 'codice_fiscale', 'partita_iva', 'pec',
        'forma_giuridica', 'sede_legale_indirizzo', 'sede_legale_citta',
        'sede_legale_provincia', 'sede_legale_cap', 'capitale_sociale',
        'capitale_versato', 'data_costituzione', 'numero_rea', 'cciaa',
        'codice_ateco', 'descrizione_attivita', 'telefono', 'email',
        'sito_web', 'note', 'is_active', 'logo_path',
    ];

    protected function casts(): array
    {
        return [
            'capitale_sociale' => 'decimal:2',
            'capitale_versato' => 'decimal:2',
            'data_costituzione' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function officers(): HasMany
    {
        return $this->hasMany(CompanyOfficer::class);
    }

    public function shareholders(): HasMany
    {
        return $this->hasMany(Shareholder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function parentRelationships(): HasMany
    {
        return $this->hasMany(CompanyRelationship::class, 'child_company_id');
    }

    public function childRelationships(): HasMany
    {
        return $this->hasMany(CompanyRelationship::class, 'parent_company_id');
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
            $q->where('denominazione', 'LIKE', "%{$term}%")
              ->orWhere('codice_fiscale', 'LIKE', "%{$term}%")
              ->orWhere('partita_iva', 'LIKE', "%{$term}%");
        });
    }

    public function getFormattedCapitaleAttribute(): string
    {
        return number_format((float) $this->capitale_sociale, 2, ',', '.');
    }

    public function getExpiringDocumentsCountAttribute(): int
    {
        return $this->documents()
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '>', now())
            ->where('expiration_date', '<=', now()->addDays(30))
            ->count();
    }

    public function getExpiredDocumentsCountAttribute(): int
    {
        return $this->documents()
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', now())
            ->count();
    }
}
