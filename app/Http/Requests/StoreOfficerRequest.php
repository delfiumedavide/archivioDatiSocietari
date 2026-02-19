<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('officers.manage');
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'ruolo' => ['required', 'string', 'max:100'],
            'data_nomina' => ['required', 'date'],
            'data_scadenza' => ['nullable', 'date', 'after:data_nomina'],
            'data_cessazione' => ['nullable', 'date', 'after:data_nomina'],
            'compenso' => ['nullable', 'numeric', 'min:0'],
            'poteri' => ['nullable', 'string', 'max:5000'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'Il membro e obbligatorio.',
            'member_id.exists' => 'Membro non trovato.',
            'ruolo.required' => 'Il ruolo e obbligatorio.',
            'data_nomina.required' => 'La data di nomina e obbligatoria.',
            'data_scadenza.after' => 'La data di scadenza deve essere successiva alla data di nomina.',
        ];
    }
}
