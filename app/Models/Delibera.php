<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delibera extends Model
{
    protected $table = 'delibere';

    protected $fillable = [
        'riunione_id', 'numero', 'oggetto', 'esito', 'note',
    ];

    public function riunione(): BelongsTo
    {
        return $this->belongsTo(Riunione::class);
    }

    public function getEsitoLabelAttribute(): string
    {
        return match ($this->esito) {
            'approvata' => 'Approvata',
            'respinta'  => 'Respinta',
            'sospesa'   => 'Sospesa',
            default     => $this->esito,
        };
    }

    public function getEsitoColorAttribute(): string
    {
        return match ($this->esito) {
            'approvata' => 'green',
            'respinta'  => 'red',
            'sospesa'   => 'yellow',
            default     => 'gray',
        };
    }
}
