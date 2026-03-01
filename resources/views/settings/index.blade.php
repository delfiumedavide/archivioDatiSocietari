@extends('layouts.app')

@section('title', 'Impostazioni')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
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

        {{-- ============================================================ --}}
        {{-- Sezione 4: Archiviazione File --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6"
             x-data="{ mode: '{{ old('storage_mode', $settings->storage_mode ?? 'local') }}', testResult: null, testing: false }">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Archiviazione File</h3>
                <p class="text-xs text-gray-500 mt-0.5">Configura dove vengono salvati i file caricati nel gestionale</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                {{-- Modalità --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-3">Modalità archiviazione</label>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="storage_mode" value="local" x-model="mode" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800">Solo locale</span>
                                <p class="text-xs text-gray-500 mt-0.5">I file vengono salvati solo sul server del gestionale (predefinito)</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="storage_mode" value="both" x-model="mode" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800">Locale + Server esterno</span>
                                <p class="text-xs text-gray-500 mt-0.5">Ogni file viene salvato sia in locale che nel percorso esterno (copia automatica)</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="storage_mode" value="external" x-model="mode" class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800">Solo server esterno</span>
                                <p class="text-xs text-gray-500 mt-0.5">I file vengono salvati e letti esclusivamente dal percorso esterno</p>
                            </div>
                        </label>
                    </div>
                    @error('storage_mode') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Percorso esterno (visibile solo se mode != local) --}}
                <div x-show="mode !== 'local'" x-transition class="border-t border-gray-100 pt-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Percorso esterno sul server
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="storage_external_path"
                               value="{{ old('storage_external_path', $settings->storage_external_path) }}"
                               class="form-input flex-1 text-sm font-mono"
                               placeholder="es. /mnt/archivio  o  /var/shared/documenti">
                        <button type="button"
                                @click="
                                    testing = true;
                                    testResult = null;
                                    fetch('{{ route('settings.test-storage') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        },
                                        body: JSON.stringify({ path: document.querySelector('[name=storage_external_path]').value })
                                    })
                                    .then(r => r.json())
                                    .then(data => { testResult = data; testing = false; })
                                    .catch(() => { testResult = { ok: false, message: 'Errore di rete.' }; testing = false; })
                                "
                                class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg transition text-sm whitespace-nowrap"
                                :disabled="testing">
                            <svg x-show="!testing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span x-text="testing ? 'Test...' : 'Testa percorso'"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Il percorso deve essere accessibile dal processo PHP del server (cartella montata, NFS, ecc.)</p>
                    @error('storage_external_path') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                    {{-- Risultato test --}}
                    <div x-show="testResult !== null" x-transition class="mt-3">
                        <div :class="testResult?.ok ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                             class="border rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
                            <svg x-show="testResult?.ok" class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <svg x-show="!testResult?.ok" class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="testResult?.message"></span>
                        </div>
                    </div>
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
