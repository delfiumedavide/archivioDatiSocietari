<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('companies.edit');
    }

    public function rules(): array
    {
        $companyId = $this->route('company')->id;

        return [
            'denominazione' => ['required', 'string', 'max:255'],
            'codice_fiscale' => ['nullable', 'string', 'max:16', Rule::unique('companies')->ignore($companyId)],
            'partita_iva' => ['nullable', 'string', 'max:11', Rule::unique('companies')->ignore($companyId)],
            'pec' => ['nullable', 'email', 'max:255'],
            'forma_giuridica' => ['nullable', 'string', 'max:100'],
            'sede_legale_indirizzo' => ['nullable', 'string', 'max:255'],
            'sede_legale_citta' => ['nullable', 'string', 'max:100'],
            'sede_legale_provincia' => ['nullable', 'string', 'max:5'],
            'sede_legale_cap' => ['nullable', 'string', 'max:5'],
            'capitale_sociale' => ['nullable', 'numeric', 'min:0', 'max:99999999999999.99'],
            'capitale_versato' => ['nullable', 'numeric', 'min:0', 'max:99999999999999.99'],
            'data_costituzione' => ['nullable', 'date', 'before_or_equal:today'],
            'numero_rea' => ['nullable', 'string', 'max:20'],
            'cciaa' => ['nullable', 'string', 'max:100'],
            'codice_ateco' => ['nullable', 'string', 'max:10'],
            'descrizione_attivita' => ['nullable', 'string', 'max:5000'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'sito_web' => ['nullable', 'url', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
