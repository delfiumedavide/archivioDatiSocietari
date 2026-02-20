<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiunionePartecipante extends Model
{
    protected $table = 'riunione_partecipanti';

    protected $fillable = [
        'riunione_id', 'member_id', 'presenza', 'note',
    ];

    public function riunione(): BelongsTo
    {
        return $this->belongsTo(Riunione::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getPresenzaLabelAttribute(): string
    {
        return match ($this->presenza) {
            'presente'  => 'Presente',
            'assente'   => 'Assente',
            'delegato'  => 'Delegato',
            default     => $this->presenza,
        };
    }

    public function getPresenzaColorAttribute(): string
    {
        return match ($this->presenza) {
            'presente'  => 'green',
            'assente'   => 'red',
            'delegato'  => 'yellow',
            default     => 'gray',
        };
    }
}
