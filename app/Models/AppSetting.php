<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    protected $fillable = [
        'app_name', 'app_subtitle', 'login_title',
        'logo_path', 'favicon_path',
        'holding_ragione_sociale', 'holding_forma_giuridica',
        'holding_codice_fiscale', 'holding_partita_iva',
        'holding_indirizzo', 'holding_citta', 'holding_provincia', 'holding_cap',
        'holding_telefono', 'holding_email', 'holding_pec',
        'holding_rea', 'holding_capitale_sociale',
        'declaration_header_title', 'declaration_header_subtitle', 'declaration_footer_text',
        'notification_emails', 'expiry_reminder_days',
        'expiry_reminder_enabled', 'expiry_reminder_time',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'holding_capitale_sociale' => 'decimal:2',
            'expiry_reminder_enabled'  => 'boolean',
        ];
    }

    public static function instance(): static
    {
        return static::firstOrCreate(['id' => 1], [
            'app_name' => 'Archivio Societario',
            'app_subtitle' => 'Gruppo di Martino',
        ]);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }

        return null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon_path && Storage::disk('public')->exists($this->favicon_path)) {
            return asset('storage/' . $this->favicon_path);
        }

        return null;
    }
}
