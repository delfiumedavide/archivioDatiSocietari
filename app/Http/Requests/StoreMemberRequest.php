<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasSection('companies');
    }

    public function rules(): array
    {
        $maxSize = config('archivio.upload.max_size_mb', 50) * 1024;
        $allowedTypes = implode(',', config('archivio.upload.allowed_types', []));

        return [
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['required', 'string', 'size:16', 'unique:members,codice_fiscale'],
            'data_nascita' => ['nullable', 'date', 'before:today'],
            'luogo_nascita' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'indirizzo_residenza' => ['nullable', 'string', 'max:255'],
            'comune_residenza' => ['nullable', 'string', 'max:150'],
            'provincia_residenza' => ['nullable', 'string', 'size:2'],
            'cap_residenza' => ['nullable', 'string', 'max:10'],
            'note' => ['nullable', 'string', 'max:5000'],
            'documento_identita_file' => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
            'documento_identita_scadenza' => ['nullable', 'date'],
            'codice_fiscale_file' => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
            'codice_fiscale_scadenza' => ['nullable', 'date'],
        ];
    }
}
