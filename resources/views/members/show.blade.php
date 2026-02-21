@extends('layouts.app')

@section('title', $member->full_name)

@section('breadcrumb')
    <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('members.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors font-medium">Membri</a>
    <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $member->full_name }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'anagrafica' }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center text-brand-700 font-bold text-lg flex-shrink-0">
                {{ strtoupper(substr($member->nome, 0, 1) . substr($member->cognome, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-sm text-gray-500 font-mono">{{ $member->codice_fiscale }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $member->is_active ? 'Attivo' : 'Inattivo' }}
                    </span>
                    @if($member->white_list)
                        @if($member->is_white_list_expired)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">WL Scaduta</span>
                        @elseif($member->is_white_list_expiring)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">WL In Scadenza</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">White List</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasPermission('membri.edit'))
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifica
            </a>
            @endif
            @if(auth()->user()->hasPermission('membri.delete'))
            <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Sei sicuro di voler eliminare questo membro? Questa azione non puo essere annullata.')">
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
            <button @click="activeTab = 'anagrafica'" :class="activeTab === 'anagrafica' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Anagrafica
            </button>
            <button @click="activeTab = 'documenti'" :class="activeTab === 'documenti' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Documenti
                @if($member->documents->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $member->documents->count() }}</span>
                @endif
            </button>
            <button @click="activeTab = 'cariche'" :class="activeTab === 'cariche' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Cariche Societarie
                @if($member->officers->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $member->officers->count() }}</span>
                @endif
            </button>
            <button @click="activeTab = 'famiglia'" :class="activeTab === 'famiglia' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-5 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Stato Famiglia
            </button>
        </nav>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Anagrafica --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'anagrafica'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Dati Personali --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Dati Personali</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cognome</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $member->cognome }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $member->nome }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Codice Fiscale</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $member->codice_fiscale }}</dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Data di Nascita</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->data_nascita ? $member->data_nascita->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sesso</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->sesso === 'M' ? 'Maschio' : ($member->sesso === 'F' ? 'Femmina' : '-') }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Luogo di Nascita</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $member->luogo_nascita_comune ?: '-' }}{{ $member->luogo_nascita_provincia ? ' (' . $member->luogo_nascita_provincia . ')' : '' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nazionalita</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->nazionalita ?: '-' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stato Civile</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->current_stato_civile)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">{{ $member->current_stato_civile }}</span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            {{-- Residenza e Domicilio --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Residenza</h3>
                    <div class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Indirizzo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->indirizzo_residenza ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Citta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->citta_residenza ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Provincia</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->provincia_residenza ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">CAP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->cap_residenza ?: '-' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                @if($member->indirizzo_domicilio)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Domicilio</h3>
                    <div class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Indirizzo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->indirizzo_domicilio }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Citta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->citta_domicilio ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Provincia</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->provincia_domicilio ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">CAP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $member->cap_domicilio ?: '-' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Contatti & White List --}}
        <div class="mt-6 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Contatti</h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Telefono</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->telefono)
                                <a href="tel:{{ $member->telefono }}" class="text-brand-600 hover:underline">{{ $member->telefono }}</a>
                            @else - @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cellulare</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->cellulare)
                                <a href="tel:{{ $member->cellulare }}" class="text-brand-600 hover:underline">{{ $member->cellulare }}</a>
                            @else - @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->email)
                                <a href="mailto:{{ $member->email }}" class="text-brand-600 hover:underline">{{ $member->email }}</a>
                            @else - @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">PEC</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($member->pec)
                                <a href="mailto:{{ $member->pec }}" class="text-brand-600 hover:underline">{{ $member->pec }}</a>
                            @else - @endif
                        </dd>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">White List</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Iscrizione</dt>
                        <dd class="mt-1">
                            @if($member->white_list)
                                @if($member->is_white_list_expired)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Scaduta</span>
                                @elseif($member->is_white_list_expiring)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">In Scadenza</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Attiva</span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Non iscritto</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scadenza</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->white_list_scadenza ? $member->white_list_scadenza->format('d/m/Y') : '-' }}</dd>
                    </div>
                </div>
            </div>

            @if($member->note)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Note</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $member->note }}</p>
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
                <h3 class="text-base font-semibold text-gray-900">Documenti del Membro</h3>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->hasPermission('documents.upload'))
                    <a href="{{ route('documents.create', ['member_id' => $member->id]) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Carica Documento
                    </a>
                    @endif
                    @if($member->documents->count() > 0)
                    <a href="{{ route('documents.index', ['member_id' => $member->id]) }}" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700 font-medium">
                        Vedi tutti
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            @if($member->documents->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($member->documents as $document)
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
                                <span class="text-xs text-gray-500">{{ $document->category->label }}</span>
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
                <p class="mt-1 text-xs text-gray-500">Non ci sono ancora documenti associati a questo membro.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Cariche Societarie --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'cariche'" x-transition>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Cariche Societarie</h3>
            </div>

            @if($member->officers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Societa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruolo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Nomina</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Scadenza</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($member->officers as $officer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('companies.show', $officer->company) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                    {{ $officer->company->denominazione }}
                                </a>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <h4 class="mt-3 text-sm font-medium text-gray-900">Nessuna carica registrata</h4>
                <p class="mt-1 text-xs text-gray-500">Questo membro non ha cariche societarie attive.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Stato Famiglia --}}
    {{-- ============================================================ --}}
    <div x-show="activeTab === 'famiglia'" x-transition>
        <div class="space-y-6">
            {{-- Current Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Stato Famiglia</h3>
                    @if(auth()->user()->hasSection('stati_famiglia'))
                    <a href="{{ route('family-status.show', $member) }}" class="inline-flex items-center gap-1 text-sm text-brand-600 hover:text-brand-700 font-medium">
                        Gestisci Dettaglio
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stato Civile Attuale</dt>
                        <dd class="mt-1">
                            @if($member->current_stato_civile)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">{{ $member->current_stato_civile }}</span>
                            @else
                                <span class="text-sm text-gray-500">Non specificato</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Variazioni Registrate</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->familyStatusChanges->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Componenti Nucleo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $member->familyMembers->where('data_fine', null)->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
