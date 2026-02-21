@extends('layouts.app')
@section('title', 'Nuova Societa')
@section('breadcrumb')
<span class="text-gray-400 font-light text-base select-none">&rsaquo;</span>
<a href="{{ route('companies.index') }}" class="text-brand-600 hover:underline">Societa</a>
<span class="text-gray-400 font-light text-base select-none">&rsaquo;</span>
<span class="text-gray-700 font-medium">Nuova</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuova Societa</h1>

    <form method="POST" action="{{ route('companies.store') }}" class="space-y-6">
        @csrf

        {{-- Dati Generali --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Dati Generali</h2></div>
            <div class="card-body space-y-4">
                <div>
                    <label for="denominazione" class="form-label">Denominazione *</label>
                    <input type="text" name="denominazione" id="denominazione" value="{{ old('denominazione') }}" class="form-input" required>
                    @error('denominazione')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="forma_giuridica" class="form-label">Forma Giuridica</label>
                        <select name="forma_giuridica" id="forma_giuridica" class="form-select">
                            <option value="">Seleziona...</option>
                            @foreach(['SRL', 'SRLS', 'SPA', 'SAPA', 'SAS', 'SNC', 'SS', 'Cooperativa', 'Consorzio', 'Impresa Individuale', 'Altro'] as $forma)
                            <option value="{{ $forma }}" {{ old('forma_giuridica') === $forma ? 'selected' : '' }}>{{ $forma }}</option>
                            @endforeach
                        </select>
                        @error('forma_giuridica')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" id="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-input" maxlength="16" placeholder="Es: 12345678901">
                        @error('codice_fiscale')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="partita_iva" class="form-label">Partita IVA</label>
                        <input type="text" name="partita_iva" id="partita_iva" value="{{ old('partita_iva') }}" class="form-input" maxlength="11" placeholder="Es: 12345678901">
                        @error('partita_iva')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="pec" class="form-label">PEC</label>
                    <input type="email" name="pec" id="pec" value="{{ old('pec') }}" class="form-input" placeholder="societa@pec.it">
                    @error('pec')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Sede Legale --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Sede Legale</h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="sede_legale_indirizzo" class="form-label">Indirizzo</label>
                        <input type="text" name="sede_legale_indirizzo" id="sede_legale_indirizzo" value="{{ old('sede_legale_indirizzo') }}" class="form-input" placeholder="Via Roma, 1">
                    </div>
                    <div>
                        <label for="sede_legale_citta" class="form-label">Citta</label>
                        <input type="text" name="sede_legale_citta" id="sede_legale_citta" value="{{ old('sede_legale_citta') }}" class="form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sede_legale_provincia" class="form-label">Provincia</label>
                            <input type="text" name="sede_legale_provincia" id="sede_legale_provincia" value="{{ old('sede_legale_provincia') }}" class="form-input" maxlength="5" placeholder="MI">
                        </div>
                        <div>
                            <label for="sede_legale_cap" class="form-label">CAP</label>
                            <input type="text" name="sede_legale_cap" id="sede_legale_cap" value="{{ old('sede_legale_cap') }}" class="form-input" maxlength="5" placeholder="20100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dati Economici --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Dati Economici e Registri</h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="capitale_sociale" class="form-label">Capitale Sociale (&euro;)</label>
                        <input type="number" name="capitale_sociale" id="capitale_sociale" value="{{ old('capitale_sociale') }}" class="form-input" step="0.01" min="0">
                    </div>
                    <div>
                        <label for="capitale_versato" class="form-label">Capitale Versato (&euro;)</label>
                        <input type="number" name="capitale_versato" id="capitale_versato" value="{{ old('capitale_versato') }}" class="form-input" step="0.01" min="0">
                    </div>
                    <div>
                        <label for="data_costituzione" class="form-label">Data Costituzione</label>
                        <input type="date" name="data_costituzione" id="data_costituzione" value="{{ old('data_costituzione') }}" class="form-input" max="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="numero_rea" class="form-label">Numero REA</label>
                        <input type="text" name="numero_rea" id="numero_rea" value="{{ old('numero_rea') }}" class="form-input">
                    </div>
                    <div>
                        <label for="cciaa" class="form-label">CCIAA</label>
                        <input type="text" name="cciaa" id="cciaa" value="{{ old('cciaa') }}" class="form-input" placeholder="Camera di Commercio di...">
                    </div>
                    <div>
                        <label for="codice_ateco" class="form-label">Codice ATECO</label>
                        <input type="text" name="codice_ateco" id="codice_ateco" value="{{ old('codice_ateco') }}" class="form-input" maxlength="10" placeholder="62.01">
                    </div>
                </div>
            </div>
        </div>

        {{-- Contatti --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Contatti</h2></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="telefono" class="form-label">Telefono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" class="form-input">
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input">
                    </div>
                    <div>
                        <label for="sito_web" class="form-label">Sito Web</label>
                        <input type="url" name="sito_web" id="sito_web" value="{{ old('sito_web') }}" class="form-input" placeholder="https://">
                    </div>
                </div>
            </div>
        </div>

        {{-- Note --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Note</h2></div>
            <div class="card-body space-y-4">
                <div>
                    <label for="descrizione_attivita" class="form-label">Descrizione Attivita</label>
                    <textarea name="descrizione_attivita" id="descrizione_attivita" rows="3" class="form-input">{{ old('descrizione_attivita') }}</textarea>
                </div>
                <div>
                    <label for="note" class="form-label">Note</label>
                    <textarea name="note" id="note" rows="3" class="form-input">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('companies.index') }}" class="btn-secondary">Annulla</a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Crea Societa
            </button>
        </div>
    </form>
</div>
@endsection
