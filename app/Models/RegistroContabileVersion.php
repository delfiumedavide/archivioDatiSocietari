<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroContabileVersion extends Model
{
    public $timestamps = false;

    protected $table = 'registro_contabile_versions';

    protected $fillable = [
        'registro_id',
        'version',
        'file_path',
        'file_size',
        'file_mime_type',
        'uploaded_by',
        'change_notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'file_size'  => 'integer',
            'version'    => 'integer',
        ];
    }

    public function registro(): BelongsTo
    {
        return $this->belongsTo(RegistroContabile::class, 'registro_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $size = $this->file_size;

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }

        return round($size / 1024, 1) . ' KB';
    }
}
