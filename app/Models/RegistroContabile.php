<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroContabile extends Model
{
    use SoftDeletes;

    protected $table = 'registri_contabili';

    protected $fillable = [
        'company_id',
        'anno',
        'mese',
        'tipo',
        'titolo',
        'note',
        'file_path',
        'file_name_original',
        'file_mime_type',
        'file_size',
        'current_version',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'anno'            => 'integer',
            'mese'            => 'integer',
            'file_size'       => 'integer',
            'current_version' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Costanti — tipi di registro
    // -------------------------------------------------------------------------

    public const TIPI = [
        // Annuali
        'libro_giornale'                => 'Libro Giornale',
        'libro_inventari'               => 'Libro Inventari',
        'libro_cespiti'                 => 'Libro Cespiti Ammortizzabili',
        'bilancio'                      => 'Fascicoli di Bilancio',
        'dichiarazione_redditi'         => 'Dichiarazione Redditi',
        'dichiarazione_iva'             => 'Dichiarazione IVA',
        'liquidazione_iva_annuale'      => 'Liquidazione IVA Annuale',
        // IVA mensili — standard
        'registro_iva_vendite'          => 'Registro IVA Vendite',
        'registro_iva_acquisti'         => 'Registro IVA Acquisti',
        'registro_corrispettivi'        => 'Registro Corrispettivi',
        'liquidazione_iva_ordinaria'    => 'Liquidazione IVA Ordinaria',
        // IVA mensili — regime del margine
        'registro_iva_vendite_margine'  => 'Registro IVA Vendite Margine',
        'registro_iva_acquisti_margine' => 'Registro IVA Acquisti Margine',
        'liquidazione_iva_margine'      => 'Liquidazione IVA Margine',
        'altro'                         => 'Altro',
    ];

    /** Tipi che si caricano mensilmente (richiedono il campo mese). */
    public const TIPI_IVA_MENSILI = [
        'registro_iva_vendite',
        'registro_iva_acquisti',
        'registro_corrispettivi',
        'liquidazione_iva_ordinaria',
        'registro_iva_vendite_margine',
        'registro_iva_acquisti_margine',
        'liquidazione_iva_margine',
    ];

    /** Tipi mensili riservati al regime del margine. */
    public const TIPI_IVA_MARGINE = [
        'registro_iva_vendite_margine',
        'registro_iva_acquisti_margine',
        'liquidazione_iva_margine',
    ];

    /** Nomi dei mesi (1-indexed). */
    public const MESI = [
        1  => 'Gennaio',
        2  => 'Febbraio',
        3  => 'Marzo',
        4  => 'Aprile',
        5  => 'Maggio',
        6  => 'Giugno',
        7  => 'Luglio',
        8  => 'Agosto',
        9  => 'Settembre',
        10 => 'Ottobre',
        11 => 'Novembre',
        12 => 'Dicembre',
    ];

    // -------------------------------------------------------------------------
    // Helper statici
    // -------------------------------------------------------------------------

    /** Solo tipi annuali (esclude IVA mensili). */
    public static function tipiAnnuali(): array
    {
        return array_diff_key(self::TIPI, array_flip(self::TIPI_IVA_MENSILI));
    }

    /** Solo tipi mensili standard (esclude margine). */
    public static function tipiMensiliStandard(): array
    {
        return array_diff_key(
            array_intersect_key(self::TIPI, array_flip(self::TIPI_IVA_MENSILI)),
            array_flip(self::TIPI_IVA_MARGINE)
        );
    }

    // -------------------------------------------------------------------------
    // Relazioni
    // -------------------------------------------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(RegistroContabileVersion::class, 'registro_id')
                    ->orderByDesc('version');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForUser($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $ids = $user->accessibleCompanyIds();

        if (empty($ids)) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('company_id', $ids);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByAnno($query, int $anno)
    {
        return $query->where('anno', $anno);
    }

    public function scopeByMese($query, int $mese)
    {
        return $query->where('mese', $mese);
    }

    public function scopeByTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getTipoLabelAttribute(): string
    {
        return self::TIPI[$this->tipo] ?? $this->tipo;
    }

    public function getMeseLabelAttribute(): ?string
    {
        return $this->mese ? (self::MESI[$this->mese] ?? null) : null;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $size = $this->file_size;

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }

        return round($size / 1024, 1) . ' KB';
    }

    public function getIsMensileAttribute(): bool
    {
        return in_array($this->tipo, self::TIPI_IVA_MENSILI);
    }
}
