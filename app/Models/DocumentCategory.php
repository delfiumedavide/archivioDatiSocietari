<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    protected $fillable = ['name', 'label', 'description', 'icon', 'scope', 'sort_order'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function scopeForCompany($query)
    {
        return $query->whereIn('scope', ['company', 'both']);
    }

    public function scopeForMember($query)
    {
        return $query->whereIn('scope', ['member', 'both']);
    }
}
