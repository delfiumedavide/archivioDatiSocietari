@extends('layouts.app')
@section('title', 'Nuovo Membro')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('members.index') }}" class="text-brand-600 hover:underline">Membri</a>
<svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Nuovo</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuovo Membro</h1>

    <form method="POST" action="{{ route('members.store') }}" class="space-y-6">
        @csrf

        {{-- Dati Personali --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Dati Personali</h2></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cognome" class="form-label">Cognome *</label>
                        <input type="text" name="cognome" id="cognome" value="{{ old('cognome') }}" class="form-input" required maxlength="100">
                        @error('cognome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" name="nome" id="nome" value="{{ old('nome') }}" class="form-input" required maxlength="100">
                        @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="codice_fiscale" class="form-label">Codice Fiscale *</label>
                        <input type="text" name="codice_fiscale" id="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-input font-mono" required maxlength="16" placeholder="RSSMRA85M01H501Z">
                        @error('codice_fiscale')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="data_nascita" class="form-label">Data di Nascita</label>
                        <input type="date" name="data_nascita" id="data_nascita" value="{{ old('data_nascita') }}" class="form-input" max="{{ date('Y-m-d') }}">
                        @error('data_nascita')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sesso" class="form-label">Sesso</label>
                        <select name="sesso" id="sesso" class="form-select">
                            <option value="">Seleziona...</option>
                            <option value="M" {{ old('sesso') === 'M' ? 'selected' : '' }}>Maschio</option>
                            <option value="F" {{ old('sesso') === 'F' ? 'selected' : '' }}>Femmina</option>
                        </select>
                        @error('sesso')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="luogo_nascita_comune" class="form-label">Comune di Nascita</label>
                        <input type="text" name="luogo_nascita_comune" id="luogo_nascita_comune" value="{{ old('luogo_nascita_comune') }}" class="form-input" maxlength="100">
                        @error('luogo_nascita_comune')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="luogo_nascita_provincia" class="form-label">Provincia di Nascita</label>
                        <input type="text" name="luogo_nascita_provincia" id="luogo_nascita_provincia" value="{{ old('luogo_nascita_provincia') }}" class="form-input" maxlength="2" placeholder="MI">
                        @error('luogo_nascita_provincia')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nazionalita" class="form-label">Nazionalita</label>
                        <input type="text" name="nazionalita" id="nazionalita" value="{{ old('nazionalita', 'Italiana') }}" class="form-input" maxlength="100">
                        @error('nazionalita')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="stato_civile" class="form-label">Stato Civile</label>
                    <select name="stato_civile" id="stato_civile" class="form-select">
                        <option value="">Seleziona...</option>
                        @foreach(['Celibe/Nubile', 'Coniugato/a', 'Separato/a', 'Divorziato/a', 'Vedovo/a', 'Unito/a Civilmente', 'Convivente'] as $stato)
                        <option value="{{ $stato }}" {{ old('stato_civile') === $stato ? 'selected' : '' }}>{{ $stato }}</option>
                        @endforeach
                    </select>
                    @error('stato_civile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Residenza --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Residenza</h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="indirizzo_residenza" class="form-label">Indirizzo</label>
                        <input type="text" name="indirizzo_residenza" id="indirizzo_residenza" value="{{ old('indirizzo_residenza') }}" class="form-input" placeholder="Via Roma, 1">
                        @error('indirizzo_residenza')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="citta_residenza" class="form-label">Citta</label>
                        <input type="text" name="citta_residenza" id="citta_residenza" value="{{ old('citta_residenza') }}" class="form-input">
                        @error('citta_residenza')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="provincia_residenza" class="form-label">Provincia</label>
                            <input type="text" name="provincia_residenza" id="provincia_residenza" value="{{ old('provincia_residenza') }}" class="form-input" maxlength="2" placeholder="MI">
                            @error('provincia_residenza')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="cap_residenza" class="form-label">CAP</label>
                            <input type="text" name="cap_residenza" id="cap_residenza" value="{{ old('cap_residenza') }}" class="form-input" maxlength="5" placeholder="20100">
                            @error('cap_residenza')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Domicilio --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Domicilio <span class="text-sm font-normal text-gray-500">(se diverso dalla residenza)</span></h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="indirizzo_domicilio" class="form-label">Indirizzo</label>
                        <input type="text" name="indirizzo_domicilio" id="indirizzo_domicilio" value="{{ old('indirizzo_domicilio') }}" class="form-input" placeholder="Via Roma, 1">
                        @error('indirizzo_domicilio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="citta_domicilio" class="form-label">Citta</label>
                        <input type="text" name="citta_domicilio" id="citta_domicilio" value="{{ old('citta_domicilio') }}" class="form-input">
                        @error('citta_domicilio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="provincia_domicilio" class="form-label">Provincia</label>
                            <input type="text" name="provincia_domicilio" id="provincia_domicilio" value="{{ old('provincia_domicilio') }}" class="form-input" maxlength="2" placeholder="MI">
                            @error('provincia_domicilio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="cap_domicilio" class="form-label">CAP</label>
                            <input type="text" name="cap_domicilio" id="cap_domicilio" value="{{ old('cap_domicilio') }}" class="form-input" maxlength="5" placeholder="20100">
                            @error('cap_domicilio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contatti --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Contatti</h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="form-label">Telefono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" class="form-input" maxlength="20">
                        @error('telefono')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="cellulare" class="form-label">Cellulare</label>
                        <input type="text" name="cellulare" id="cellulare" value="{{ old('cellulare') }}" class="form-input" maxlength="20">
                        @error('cellulare')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pec" class="form-label">PEC</label>
                        <input type="email" name="pec" id="pec" value="{{ old('pec') }}" class="form-input" placeholder="nome@pec.it">
                        @error('pec')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- White List --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">White List</h2></div>
            <div class="card-body" x-data="{ whiteList: {{ old('white_list') ? 'true' : 'false' }} }">
                <div class="flex items-center gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="white_list" value="0">
                        <input type="checkbox" name="white_list" value="1" x-model="whiteList" class="form-checkbox rounded text-brand-600" {{ old('white_list') ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Iscritto alla White List</span>
                    </label>
                </div>
                <div x-show="whiteList" x-transition class="max-w-xs">
                    <label for="white_list_scadenza" class="form-label">Data Scadenza White List</label>
                    <input type="date" name="white_list_scadenza" id="white_list_scadenza" value="{{ old('white_list_scadenza') }}" class="form-input">
                    @error('white_list_scadenza')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Note --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Note</h2></div>
            <div class="card-body">
                <textarea name="note" id="note" rows="3" class="form-input">{{ old('note') }}</textarea>
                @error('note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('members.index') }}" class="btn-secondary">Annulla</a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Crea Membro
            </button>
        </div>
    </form>
</div>
@endsection
