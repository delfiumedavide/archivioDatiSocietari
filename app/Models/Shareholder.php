<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shareholder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'tipo', 'nome', 'codice_fiscale',
        'quota_percentuale', 'quota_valore', 'data_ingresso',
        'data_uscita', 'diritti_voto', 'note',
    ];

    protected function casts(): array
    {
        return [
            'quota_percentuale' => 'decimal:2',
            'quota_valore' => 'decimal:2',
            'diritti_voto' => 'decimal:2',
            'data_ingresso' => 'date',
            'data_uscita' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('data_uscita');
    }

    public function getIsPersonaFisicaAttribute(): bool
    {
        return $this->tipo === 'persona_fisica';
    }
}
