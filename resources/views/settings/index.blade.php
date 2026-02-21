@extends('layouts.app')

@section('title', 'Impostazioni')

@section('breadcrumb')
    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Impostazioni</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Impostazioni</h1>
        <p class="mt-1 text-sm text-gray-500">Gestisci branding, dati della holding e intestazione dichiarazioni</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        {{-- ============================================================ --}}
        {{-- Sezione 1: Branding --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Branding</h3>
                <p class="text-xs text-gray-500 mt-0.5">Nome, sottotitolo e logo del gestionale</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nome Applicazione *</label>
                        <input type="text" name="app_name" value="{{ old('app_name', $settings->app_name) }}" required class="form-input w-full text-sm" placeholder="Archivio Societario">
                        @error('app_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sottotitolo *</label>
                        <input type="text" name="app_subtitle" value="{{ old('app_subtitle', $settings->app_subtitle) }}" required class="form-input w-full text-sm" placeholder="Gruppo di Martino">
                        @error('app_subtitle') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Titolo Pagina Login</label>
                    <input type="text" name="login_title" value="{{ old('login_title', $settings->login_title) }}" class="form-input w-full text-sm" placeholder="Se vuoto, usa il Nome Applicazione">
                    @error('login_title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Logo Upload --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Logo</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 overflow-hidden flex-shrink-0">
                                <img src="{{ $settings->logo_url ?? asset('images/logo-icon.svg') }}" alt="Logo" class="max-w-full max-h-full object-contain">
                            </div>
                            <div class="space-y-2">
                                <div class="text-xs text-gray-500">SVG, PNG, JPG — max 2MB</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Favicon</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 overflow-hidden flex-shrink-0">
                                <img src="{{ $settings->favicon_url ?? asset('images/logo-icon.svg') }}" alt="Favicon" class="max-w-full max-h-full object-contain">
                            </div>
                            <div class="text-xs text-gray-500">SVG, PNG, ICO — max 1MB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Sezione 2: Dati Holding --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Dati Holding</h3>
                <p class="text-xs text-gray-500 mt-0.5">Informazioni societarie della holding</p>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ragione Sociale</label>
                        <input type="text" name="holding_ragione_sociale" value="{{ old('holding_ragione_sociale', $settings->holding_ragione_sociale) }}" class="form-input w-full text-sm">
                        @error('holding_ragione_sociale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Forma Giuridica</label>
                        <select name="holding_forma_giuridica" class="form-select w-full text-sm">
                            <option value="">Seleziona...</option>
                            @foreach(['S.p.A.', 'S.r.l.', 'S.r.l.s.', 'S.a.s.', 'S.n.c.', 'S.a.p.a.', 'Cooperativa', 'Consorzio', 'Altro'] as $forma)
                            <option value="{{ $forma }}" {{ old('holding_forma_giuridica', $settings->holding_forma_giuridica) === $forma ? 'selected' : '' }}>{{ $forma }}</option>
                            @endforeach
                        </select>
                        @error('holding_forma_giuridica') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Codice Fiscale</label>
                        <input type="text" name="holding_codice_fiscale" value="{{ old('holding_codice_fiscale', $settings->holding_codice_fiscale) }}" class="form-input w-full text-sm font-mono" maxlength="16">
                        @error('holding_codice_fiscale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Partita IVA</label>
                        <input type="text" name="holding_partita_iva" value="{{ old('holding_partita_iva', $settings->holding_partita_iva) }}" class="form-input w-full text-sm font-mono" maxlength="11">
                        @error('holding_partita_iva') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Indirizzo</label>
                    <input type="text" name="holding_indirizzo" value="{{ old('holding_indirizzo', $settings->holding_indirizzo) }}" class="form-input w-full text-sm">
                    @error('holding_indirizzo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Città</label>
                        <input type="text" name="holding_citta" value="{{ old('holding_citta', $settings->holding_citta) }}" class="form-input w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Prov.</label>
                        <input type="text" name="holding_provincia" value="{{ old('holding_provincia', $settings->holding_provincia) }}" class="form-input w-full text-sm" maxlength="2">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">CAP</label>
                        <input type="text" name="holding_cap" value="{{ old('holding_cap', $settings->holding_cap) }}" class="form-input w-full text-sm" maxlength="5">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Telefono</label>
                        <input type="text" name="holding_telefono" value="{{ old('holding_telefono', $settings->holding_telefono) }}" class="form-input w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="holding_email" value="{{ old('holding_email', $settings->holding_email) }}" class="form-input w-full text-sm">
                        @error('holding_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">PEC</label>
                        <input type="email" name="holding_pec" value="{{ old('holding_pec', $settings->holding_pec) }}" class="form-input w-full text-sm">
                        @error('holding_pec') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Numero REA</label>
                        <input type="text" name="holding_rea" value="{{ old('holding_rea', $settings->holding_rea) }}" class="form-input w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Capitale Sociale (EUR)</label>
                        <input type="number" step="0.01" name="holding_capitale_sociale" value="{{ old('holding_capitale_sociale', $settings->holding_capitale_sociale) }}" class="form-input w-full text-sm" min="0">
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Sezione 3: Intestazione Dichiarazioni --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Intestazione Dichiarazioni PDF</h3>
                <p class="text-xs text-gray-500 mt-0.5">Testi che compaiono nell'intestazione e nel footer dei PDF delle dichiarazioni stato famiglia</p>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Titolo Intestazione</label>
                        <input type="text" name="declaration_header_title" value="{{ old('declaration_header_title', $settings->declaration_header_title) }}" class="form-input w-full text-sm" placeholder="es. Gruppo Di Martino">
                        @error('declaration_header_title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sottotitolo Intestazione</label>
                        <input type="text" name="declaration_header_subtitle" value="{{ old('declaration_header_subtitle', $settings->declaration_header_subtitle) }}" class="form-input w-full text-sm" placeholder="es. Archivio Societario">
                        @error('declaration_header_subtitle') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Testo Footer</label>
                    <input type="text" name="declaration_footer_text" value="{{ old('declaration_footer_text', $settings->declaration_footer_text) }}" class="form-input w-full text-sm" placeholder="es. Generato dal sistema Archivio Societario">
                    @error('declaration_footer_text') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-2.5 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Salva Impostazioni
            </button>
        </div>
    </form>

    {{-- ============================================================ --}}
    {{-- Upload Logo e Favicon (form separati) --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        {{-- Upload Logo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Carica Logo</h3>
            </div>
            <div class="px-6 py-4">
                <form method="POST" action="{{ route('settings.upload-logo') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="logo" accept=".svg,.png,.jpg,.jpeg,.webp" required class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    @error('logo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <div class="flex items-center gap-2 mt-3">
                        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Carica
                        </button>
                        @if($settings->logo_path)
                        <form method="POST" action="{{ route('settings.remove-logo') }}" class="inline" onsubmit="return confirm('Rimuovere il logo personalizzato?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium px-3 py-2">Rimuovi</button>
                        </form>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Upload Favicon --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Carica Favicon</h3>
            </div>
            <div class="px-6 py-4">
                <form method="POST" action="{{ route('settings.upload-favicon') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="favicon" accept=".svg,.png,.ico" required class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    @error('favicon') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <button type="submit" class="mt-3 inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Carica
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
