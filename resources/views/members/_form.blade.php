@php
    $isEdit = isset($member);
    $identityDocument = $isEdit ? $member->identity_document : null;
    $taxCodeDocument = $isEdit ? $member->tax_code_document : null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" value="{{ old('nome', $member->nome ?? '') }}" required class="form-input">
        @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Cognome *</label>
        <input type="text" name="cognome" value="{{ old('cognome', $member->cognome ?? '') }}" required class="form-input">
        @error('cognome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Codice Fiscale *</label>
        <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $member->codice_fiscale ?? '') }}" required maxlength="16" class="form-input uppercase">
        @error('codice_fiscale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Data Nascita</label>
        <input type="date" name="data_nascita" value="{{ old('data_nascita', isset($member->data_nascita) ? $member->data_nascita?->format('Y-m-d') : '') }}" class="form-input">
        @error('data_nascita') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Luogo Nascita</label>
        <input type="text" name="luogo_nascita" value="{{ old('luogo_nascita', $member->luogo_nascita ?? '') }}" class="form-input">
        @error('luogo_nascita') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" class="form-input">
        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Telefono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $member->telefono ?? '') }}" class="form-input">
        @error('telefono') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Indirizzo Residenza</label>
        <input type="text" name="indirizzo_residenza" value="{{ old('indirizzo_residenza', $member->indirizzo_residenza ?? '') }}" class="form-input">
        @error('indirizzo_residenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Comune Residenza</label>
        <input type="text" name="comune_residenza" value="{{ old('comune_residenza', $member->comune_residenza ?? '') }}" class="form-input">
        @error('comune_residenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia_residenza" value="{{ old('provincia_residenza', $member->provincia_residenza ?? '') }}" maxlength="2" class="form-input uppercase">
            @error('provincia_residenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">CAP</label>
            <input type="text" name="cap_residenza" value="{{ old('cap_residenza', $member->cap_residenza ?? '') }}" class="form-input">
            @error('cap_residenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Note</label>
        <textarea name="note" rows="3" class="form-input">{{ old('note', $member->note ?? '') }}</textarea>
        @error('note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-900">Documento Identita</h3>
        @if($identityDocument)
            <p class="text-xs text-gray-500 mt-1">File attuale: {{ $identityDocument->file_name_original }}</p>
            <a href="{{ route('members.documents.download', [$member, 'documento_identita']) }}" class="text-xs text-brand-600 hover:underline">Scarica documento</a>
        @endif
        <div class="mt-3">
            <label class="form-label">{{ $isEdit ? 'Sostituisci file' : 'File *' }}</label>
            <input type="file" name="documento_identita_file" {{ $isEdit ? '' : 'required' }} class="form-input">
            @error('documento_identita_file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mt-3">
            <label class="form-label">Data Scadenza</label>
            <input type="date" name="documento_identita_scadenza" value="{{ old('documento_identita_scadenza', $identityDocument?->expiration_date?->format('Y-m-d')) }}" class="form-input">
            @error('documento_identita_scadenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-semibold text-gray-900">Documento Codice Fiscale</h3>
        @if($taxCodeDocument)
            <p class="text-xs text-gray-500 mt-1">File attuale: {{ $taxCodeDocument->file_name_original }}</p>
            <a href="{{ route('members.documents.download', [$member, 'codice_fiscale']) }}" class="text-xs text-brand-600 hover:underline">Scarica documento</a>
        @endif
        <div class="mt-3">
            <label class="form-label">{{ $isEdit ? 'Sostituisci file' : 'File *' }}</label>
            <input type="file" name="codice_fiscale_file" {{ $isEdit ? '' : 'required' }} class="form-input">
            @error('codice_fiscale_file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mt-3">
            <label class="form-label">Data Scadenza</label>
            <input type="date" name="codice_fiscale_scadenza" value="{{ old('codice_fiscale_scadenza', $taxCodeDocument?->expiration_date?->format('Y-m-d')) }}" class="form-input">
            @error('codice_fiscale_scadenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
