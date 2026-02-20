<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Riunione extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'tipo', 'data_ora', 'luogo', 'status',
        'ordine_del_giorno', 'convocazione_path', 'verbale_path',
        'note', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'data_ora' => 'datetime',
        ];
    }

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function delibere(): HasMany
    {
        return $this->hasMany(Delibera::class)->orderBy('numero');
    }

    public function partecipanti(): HasMany
    {
        return $this->hasMany(RiunionePartecipante::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'riunione_partecipanti')
            ->withPivot('presenza', 'note')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('data_ora', '>=', now())->where('status', '!=', 'annullata');
    }

    public function scopePast($query)
    {
        return $query->where('data_ora', '<', now());
    }

    public function scopeByTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeMissingVerbale($query)
    {
        return $query->where('status', 'svolta')->whereNull('verbale_path');
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'cda'                     => 'Consiglio di Amministrazione',
            'collegio_sindacale'      => 'Collegio Sindacale',
            'assemblea_ordinaria'     => 'Assemblea Ordinaria',
            'assemblea_straordinaria' => 'Assemblea Straordinaria',
            default                   => $this->tipo,
        };
    }

    public function getTipoShortAttribute(): string
    {
        return match ($this->tipo) {
            'cda'                     => 'CDA',
            'collegio_sindacale'      => 'Collegio Sindacale',
            'assemblea_ordinaria'     => 'Assemblea Ordinaria',
            'assemblea_straordinaria' => 'Assemblea Straordinaria',
            default                   => $this->tipo,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'programmata' => 'Programmata',
            'convocata'   => 'Convocata',
            'svolta'      => 'Svolta',
            'annullata'   => 'Annullata',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'programmata' => 'gray',
            'convocata'   => 'blue',
            'svolta'      => 'green',
            'annullata'   => 'red',
            default       => 'gray',
        };
    }

    public function getHasConvocazioneAttribute(): bool
    {
        return !empty($this->convocazione_path);
    }

    public function getHasVerbaleAttribute(): bool
    {
        return !empty($this->verbale_path);
    }

    public function getTipoColorAttribute(): string
    {
        return match ($this->tipo) {
            'cda'                     => 'brand',
            'collegio_sindacale'      => 'purple',
            'assemblea_ordinaria'     => 'teal',
            'assemblea_straordinaria' => 'orange',
            default                   => 'gray',
        };
    }
}
