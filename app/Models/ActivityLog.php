<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'properties', 'ip_address', 'user_agent', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Creato',
            'updated' => 'Modificato',
            'deleted' => 'Eliminato',
            'downloaded' => 'Scaricato',
            'uploaded' => 'Caricato',
            'login' => 'Accesso',
            'logout' => 'Uscita',
            'login_failed' => 'Accesso fallito',
            default => $this->action,
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created', 'uploaded' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'downloaded' => 'purple',
            'login' => 'emerald',
            'logout' => 'gray',
            'login_failed' => 'red',
            default => 'gray',
        };
    }
}
