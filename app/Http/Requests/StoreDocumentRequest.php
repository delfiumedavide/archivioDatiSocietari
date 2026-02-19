<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('documents.upload');
    }

    public function rules(): array
    {
        $maxSize = config('archivio.upload.max_size_mb', 50) * 1024;
        $allowedTypes = implode(',', config('archivio.upload.allowed_types', []));

        return [
            'company_id' => ['nullable', 'required_without:member_id', 'exists:companies,id'],
            'member_id' => ['nullable', 'required_without:company_id', 'exists:members,id'],
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'file' => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
            'expiration_date' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Il file è obbligatorio.',
            'file.max' => 'Il file non può superare i ' . config('archivio.upload.max_size_mb', 50) . ' MB.',
            'file.mimes' => 'Formato file non consentito. Formati ammessi: ' . implode(', ', config('archivio.upload.allowed_types', [])),
            'company_id.required_without' => 'Selezionare una societa o un membro.',
            'company_id.exists' => 'Societa non trovata.',
            'member_id.required_without' => 'Selezionare una societa o un membro.',
            'member_id.exists' => 'Membro non trovato.',
            'document_category_id.required' => 'La categoria è obbligatoria.',
            'title.required' => 'Il titolo è obbligatorio.',
            'expiration_date.after' => 'La data di scadenza deve essere futura.',
        ];
    }
}
