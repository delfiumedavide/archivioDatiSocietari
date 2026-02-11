<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRelationship extends Model
{
    protected $fillable = [
        'parent_company_id', 'child_company_id', 'relationship_type',
        'quota_percentuale', 'data_inizio', 'data_fine', 'note',
    ];

    protected function casts(): array
    {
        return [
            'quota_percentuale' => 'decimal:2',
            'data_inizio' => 'date',
            'data_fine' => 'date',
        ];
    }

    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function childCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'child_company_id');
    }
}
