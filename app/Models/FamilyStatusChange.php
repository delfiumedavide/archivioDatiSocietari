<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyStatusChange extends Model
{
    protected $fillable = [
        'member_id', 'stato_civile', 'data_variazione', 'note', 'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'data_variazione' => 'date',
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
}
