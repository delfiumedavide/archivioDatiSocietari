<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('membri.manage');
    }

    public function rules(): array
    {
        $memberId = $this->route('member')?->id;

        return [
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['required', 'string', 'max:16', Rule::unique('members')->ignore($memberId)],
            'data_nascita' => ['nullable', 'date', 'before:today'],
            'luogo_nascita_comune' => ['nullable', 'string', 'max:100'],
            'luogo_nascita_provincia' => ['nullable', 'string', 'max:2'],
            'nazionalita' => ['nullable', 'string', 'max:100'],
            'sesso' => ['nullable', 'in:M,F'],
            'stato_civile' => ['nullable', 'string', 'max:50'],
            'indirizzo_residenza' => ['nullable', 'string', 'max:255'],
            'citta_residenza' => ['nullable', 'string', 'max:100'],
            'provincia_residenza' => ['nullable', 'string', 'max:2'],
            'cap_residenza' => ['nullable', 'string', 'max:5'],
            'indirizzo_domicilio' => ['nullable', 'string', 'max:255'],
            'citta_domicilio' => ['nullable', 'string', 'max:100'],
            'provincia_domicilio' => ['nullable', 'string', 'max:2'],
            'cap_domicilio' => ['nullable', 'string', 'max:5'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'cellulare' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'pec' => ['nullable', 'email', 'max:255'],
            'white_list' => ['boolean'],
            'white_list_scadenza' => ['nullable', 'date', 'required_if:white_list,true'],
            'note' => ['nullable', 'string', 'max:10000'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Il nome e obbligatorio.',
            'cognome.required' => 'Il cognome e obbligatorio.',
            'codice_fiscale.required' => 'Il codice fiscale e obbligatorio.',
            'codice_fiscale.unique' => 'Questo codice fiscale e gia presente nel sistema.',
            'data_nascita.before' => 'La data di nascita deve essere antecedente ad oggi.',
            'white_list_scadenza.required_if' => 'La data scadenza white list e obbligatoria quando la white list e attiva.',
        ];
    }
}
