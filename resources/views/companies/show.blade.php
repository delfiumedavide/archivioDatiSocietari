@extends('layouts.app')

@section('title', $company->denominazione)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('companies.index') }}" class="hover:text-brand-600">Societa</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700 font-medium">{{ $company->denominazione }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'dati' }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">{{ $company->denominazione }}</h1>
            @if($company->forma_giuridica)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gold-100 text-gold-800">
                {{ $company->forma_giuridica }}
            </span>
            @endif
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $company->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $company->is_active ? 'Attiva' : 'Inattiva' }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasPermission('companies.edit'))
            <a href="{{ route('companies.edit', $company) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifica
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('Sei sicuro di voler eliminare questa societa? Questa azione non puo essere annullata.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Elimina
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-0 -mb-px overflow-x-auto">
            <button @click="activeTab = 'dati'" :class="activeTab === 'dati' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Dati Aziendali
            </button>
            <button @click="activeTab = 'cariche'" :class="activeTab === 'cariche' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Cariche Societarie
                @if($company->officers->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $company->officers->count() }}</span>
                @endif
            </button>
            <button @click="activeTab = 'soci'" :class="activeTab === 'soci' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Soci
                @if($company->shareholders->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $company->shareholders->count() }}</span>
                @endif
            </button>
            <button @click="activeTab = 'documenti'" :class="activeTab === 'documenti' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Documenti
                @if($company->documents->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $company->documents->count() }}</span>
                @endif
            </button>
            <button @click="activeTab = 'relazioni'" :class="activeTab === 'relazioni' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Relazioni
            </button>
        </nav>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Dati Aziendali --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'dati'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Column --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Dati Identificativi</h3>

                <div class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Denominazione</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $company->denominazione }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Codice Fiscale</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->codice_fiscale ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Partita IVA</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->partita_iva ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">PEC</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->pec ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Forma Giuridica</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($company->forma_giuridica)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gold-100 text-gold-800">{{ $company->forma_giuridica }}</span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Codice ATECO</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->codice_ateco ?: '-' }}</dd>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Sede Legale</h3>
                    <div class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Indirizzo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->sede_legale_indirizzo ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Citta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->sede_legale_citta ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Provincia</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->sede_legale_provincia ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">CAP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->sede_legale_cap ?: '-' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Dati Economici e Registri</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Capitale Sociale</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->capitale_sociale ? '€ ' . $company->formatted_capitale : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Capitale Versato</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->capitale_versato ? '€ ' . number_format((float)$company->capitale_versato, 2, ',', '.') : '-' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Data Costituzione</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->data_costituzione ? $company->data_costituzione->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Numero REA</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->numero_rea ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">CCIAA</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->cciaa ?: '-' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full width sections --}}
        <div class="mt-6 space-y-6">
            @if($company->descrizione_attivita)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Descrizione Attivita</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $company->descrizione_attivita }}</p>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Contatti</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Telefono</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($company->telefono)
                                <a href="tel:{{ $company->telefono }}" class="text-brand-600 hover:underline">{{ $company->telefono }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($company->email)
                                <a href="mailto:{{ $company->email }}" class="text-brand-600 hover:underline">{{ $company->email }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sito Web</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($company->sito_web)
                                <a href="{{ $company->sito_web }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline">{{ $company->sito_web }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            @if($company->note)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Note</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $company->note }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Cariche Societarie --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'cariche'" x-transition x-data="{ showAddOfficer: false }">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Cariche Societarie</h3>
                @if(auth()->user()->hasPermission('companies.edit'))
                <button @click="showAddOfficer = !showAddOfficer" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Aggiungi Carica
                </button>
                @endif
            </div>

            {{-- Add Officer Form --}}
            <div x-show="showAddOfficer" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="POST" action="{{ route('companies.officers.store', $company) }}">
                    @csrf
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Nuova Carica</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nome *</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required class="form-input w-full text-sm">
                            @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Cognome *</label>
                            <input type="text" name="cognome" value="{{ old('cognome') }}" required class="form-input w-full text-sm">
                            @error('cognome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Codice Fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-input w-full text-sm" maxlength="16">
                            @error('codice_fiscale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ruolo *</label>
                            <select name="ruolo" required class="form-select w-full text-sm">
                                <option value="">Seleziona ruolo</option>
                                <option value="Amministratore Unico" {{ old('ruolo') === 'Amministratore Unico' ? 'selected' : '' }}>Amministratore Unico</option>
                                <option value="Presidente CdA" {{ old('ruolo') === 'Presidente CdA' ? 'selected' : '' }}>Presidente CdA</option>
                                <option value="Vice Presidente" {{ old('ruolo') === 'Vice Presidente' ? 'selected' : '' }}>Vice Presidente</option>
                                <option value="Consigliere" {{ old('ruolo') === 'Consigliere' ? 'selected' : '' }}>Consigliere</option>
                                <option value="Amministratore Delegato" {{ old('ruolo') === 'Amministratore Delegato' ? 'selected' : '' }}>Amministratore Delegato</option>
                                <option value="Sindaco Effettivo" {{ old('ruolo') === 'Sindaco Effettivo' ? 'selected' : '' }}>Sindaco Effettivo</option>
                                <option value="Sindaco Supplente" {{ old('ruolo') === 'Sindaco Supplente' ? 'selected' : '' }}>Sindaco Supplente</option>
                                <option value="Presidente Collegio Sindacale" {{ old('ruolo') === 'Presidente Collegio Sindacale' ? 'selected' : '' }}>Presidente Collegio Sindacale</option>
                                <option value="Revisore Legale" {{ old('ruolo') === 'Revisore Legale' ? 'selected' : '' }}>Revisore Legale</option>
                                <option value="Procuratore" {{ old('ruolo') === 'Procuratore' ? 'selected' : '' }}>Procuratore</option>
                                <option value="Direttore Generale" {{ old('ruolo') === 'Direttore Generale' ? 'selected' : '' }}>Direttore Generale</option>
                                <option value="Liquidatore" {{ old('ruolo') === 'Liquidatore' ? 'selected' : '' }}>Liquidatore</option>
                            </select>
                            @error('ruolo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Data Nomina *</label>
                            <input type="date" name="data_nomina" value="{{ old('data_nomina') }}" required class="form-input w-full text-sm">
                            @error('data_nomina') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Data Scadenza</label>
                            <input type="date" name="data_scadenza" value="{{ old('data_scadenza') }}" class="form-input w-full text-sm">
                            @error('data_scadenza') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Compenso (EUR)</label>
                            <input type="number" name="compenso" value="{{ old('compenso') }}" step="0.01" min="0" class="form-input w-full text-sm" placeholder="0,00">
                            @error('compenso') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                            <input type="text" name="note" value="{{ old('note') }}" class="form-input w-full text-sm">
                            @error('note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Salva Carica
                        </button>
                        <button type="button" @click="showAddOfficer = false" class="text-sm text-gray-500 hover:text-gray-700">Annulla</button>
                    </div>
                </form>
            </div>

            {{-- Officers Table --}}
            @if($company->officers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruolo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Nomina</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Scadenza</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($company->officers as $officer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $officer->full_name }}</div>
                                @if($officer->codice_fiscale)
                                <div class="text-xs text-gray-500">CF: {{ $officer->codice_fiscale }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $officer->ruolo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $officer->data_nomina ? $officer->data_nomina->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $officer->data_scadenza ? $officer->data_scadenza->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($officer->data_cessazione)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Cessato</span>
                                @elseif($officer->is_expired)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Scaduto</span>
                                @elseif($officer->is_expiring)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">In Scadenza</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Attivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if(auth()->user()->hasPermission('companies.edit'))
                                <form method="POST" action="{{ route('officers.destroy', $officer) }}" class="inline" onsubmit="return confirm('Eliminare questa carica?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" title="Elimina">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h4 class="mt-3 text-sm font-medium text-gray-900">Nessuna carica registrata</h4>
                <p class="mt-1 text-xs text-gray-500">Aggiungi la prima carica societaria utilizzando il pulsante sopra.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Soci --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'soci'" x-transition x-data="{ showAddShareholder: false }">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Soci</h3>
                @if(auth()->user()->hasPermission('companies.edit'))
                <button @click="showAddShareholder = !showAddShareholder" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Aggiungi Socio
                </button>
                @endif
            </div>

            {{-- Add Shareholder Form --}}
            <div x-show="showAddShareholder" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="POST" action="{{ route('companies.shareholders.store', $company) }}">
                    @csrf
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Nuovo Socio</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nome / Ragione Sociale *</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required class="form-input w-full text-sm">
                            @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo *</label>
                            <select name="tipo" required class="form-select w-full text-sm">
                                <option value="">Seleziona tipo</option>
                                <option value="persona_fisica" {{ old('tipo') === 'persona_fisica' ? 'selected' : '' }}>Persona Fisica</option>
                                <option value="persona_giuridica" {{ old('tipo') === 'persona_giuridica' ? 'selected' : '' }}>Persona Giuridica</option>
                            </select>
                            @error('tipo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Codice Fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-input w-full text-sm" maxlength="16">
                            @error('codice_fiscale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quota % *</label>
                            <input type="number" name="quota_percentuale" value="{{ old('quota_percentuale') }}" required step="0.01" min="0" max="100" class="form-input w-full text-sm" placeholder="0,00">
                            @error('quota_percentuale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quota Valore (EUR)</label>
                            <input type="number" name="quota_valore" value="{{ old('quota_valore') }}" step="0.01" min="0" class="form-input w-full text-sm" placeholder="0,00">
                            @error('quota_valore') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Data Ingresso *</label>
                            <input type="date" name="data_ingresso" value="{{ old('data_ingresso') }}" required class="form-input w-full text-sm">
                            @error('data_ingresso') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Diritti di Voto (%)</label>
                            <input type="number" name="diritti_voto" value="{{ old('diritti_voto') }}" step="0.01" min="0" max="100" class="form-input w-full text-sm" placeholder="0,00">
                            @error('diritti_voto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                            <input type="text" name="note" value="{{ old('note') }}" class="form-input w-full text-sm">
                            @error('note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Salva Socio
                        </button>
                        <button type="button" @click="showAddShareholder = false" class="text-sm text-gray-500 hover:text-gray-700">Annulla</button>
                    </div>
                </form>
            </div>

            {{-- Shareholders Table --}}
            @if($company->shareholders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quota %</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quota Valore</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Ingresso</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Diritti Voto</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($company->shareholders as $shareholder)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $shareholder->nome }}</div>
                                @if($shareholder->codice_fiscale)
                                <div class="text-xs text-gray-500">CF: {{ $shareholder->codice_fiscale }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $shareholder->tipo === 'persona_fisica' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $shareholder->tipo === 'persona_fisica' ? 'Persona Fisica' : 'Persona Giuridica' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 max-w-[100px]">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-brand-600 h-2 rounded-full" style="width: {{ min((float)$shareholder->quota_percentuale, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format((float)$shareholder->quota_percentuale, 2, ',', '.') }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $shareholder->quota_valore ? '€ ' . number_format((float)$shareholder->quota_valore, 2, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $shareholder->data_ingresso ? $shareholder->data_ingresso->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $shareholder->diritti_voto ? number_format((float)$shareholder->diritti_voto, 2, ',', '.') . '%' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if(auth()->user()->hasPermission('companies.edit'))
                                <form method="POST" action="{{ route('shareholders.destroy', $shareholder) }}" class="inline" onsubmit="return confirm('Eliminare questo socio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" title="Elimina">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <h4 class="mt-3 text-sm font-medium text-gray-900">Nessun socio registrato</h4>
                <p class="mt-1 text-xs text-gray-500">Aggiungi il primo socio utilizzando il pulsante sopra.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Documenti --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'documenti'" x-transition>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Documenti</h3>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->hasPermission('documents.upload'))
                    <a href="{{ route('documents.create', ['company_id' => $company->id]) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Carica Documento
                    </a>
                    @endif
                    <a href="{{ route('documents.index', ['company_id' => $company->id]) }}" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700 font-medium">
                        Vedi tutti
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            @if($company->documents->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($company->documents->take(10) as $document)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-brand-50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('documents.show', $document) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600 transition truncate block">
                                {{ $document->title }}
                            </a>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($document->category)
                                <span class="text-xs text-gray-500">{{ $document->category->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        @if($document->expiration_date)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $document->status_color }}-100 text-{{ $document->status_color }}-700">
                            {{ $document->status_label }} - {{ $document->expiration_date->format('d/m/Y') }}
                        </span>
                        @endif
                        <a href="{{ route('documents.download', $document) }}" class="text-gray-400 hover:text-brand-600 transition" title="Scarica">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h4 class="mt-3 text-sm font-medium text-gray-900">Nessun documento</h4>
                <p class="mt-1 text-xs text-gray-500">Non ci sono ancora documenti associati a questa societa.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Relazioni --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'relazioni'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Societa Controllate (child relationships) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Societa Controllate</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Societa in cui {{ $company->denominazione }} detiene una partecipazione</p>
                </div>
                @if($company->childRelationships->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($company->childRelationships as $relation)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('companies.show', $relation->childCompany) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                    {{ $relation->childCompany->denominazione }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ $relation->relationship_type }}
                                    </span>
                                    @if($relation->data_inizio)
                                    <span class="text-xs text-gray-500">dal {{ $relation->data_inizio->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($relation->quota_percentuale)
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ number_format((float)$relation->quota_percentuale, 2, ',', '.') }}%</span>
                                <p class="text-xs text-gray-500">partecipazione</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Nessuna societa controllata</p>
                </div>
                @endif
            </div>

            {{-- Societa Controllanti (parent relationships) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Societa Controllanti</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Societa che detengono una partecipazione in {{ $company->denominazione }}</p>
                </div>
                @if($company->parentRelationships->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($company->parentRelationships as $relation)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('companies.show', $relation->parentCompany) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                    {{ $relation->parentCompany->denominazione }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ $relation->relationship_type }}
                                    </span>
                                    @if($relation->data_inizio)
                                    <span class="text-xs text-gray-500">dal {{ $relation->data_inizio->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($relation->quota_percentuale)
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ number_format((float)$relation->quota_percentuale, 2, ',', '.') }}%</span>
                                <p class="text-xs text-gray-500">partecipazione</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Nessuna societa controllante</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
