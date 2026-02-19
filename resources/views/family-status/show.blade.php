@extends('layouts.app')

@section('title', 'Stato Famiglia - ' . $member->full_name)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('family-status.index') }}" class="hover:text-brand-600">Stati Famiglia</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700 font-medium">{{ $member->full_name }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ showAddChange: false, showAddMember: false, editingMember: null }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center text-brand-700 font-bold text-lg flex-shrink-0">
                {{ strtoupper(substr($member->nome, 0, 1) . substr($member->cognome, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-sm text-gray-500">Stato Famiglia</span>
                    @if($member->current_stato_civile)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">{{ $member->current_stato_civile }}</span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('members.show', $member) }}" class="inline-flex items-center gap-2 text-sm text-brand-600 hover:text-brand-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Scheda Membro
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- Storico Variazioni Stato Civile --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Storico Variazioni Stato Civile</h3>
            @if(auth()->user()->hasPermission('stati_famiglia.manage'))
            <button @click="showAddChange = !showAddChange" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuova Variazione
            </button>
            @endif
        </div>

        {{-- Add Status Change Form --}}
        <div x-show="showAddChange" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="POST" action="{{ route('family-status.store-change', $member) }}">
                @csrf
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Registra Variazione Stato Civile</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nuovo Stato Civile *</label>
                        <select name="stato_civile" required class="form-select w-full text-sm">
                            <option value="">Seleziona...</option>
                            @foreach(['Celibe/Nubile', 'Coniugato/a', 'Separato/a', 'Divorziato/a', 'Vedovo/a', 'Unito/a Civilmente', 'Convivente'] as $stato)
                            <option value="{{ $stato }}" {{ old('stato_civile') === $stato ? 'selected' : '' }}>{{ $stato }}</option>
                            @endforeach
                        </select>
                        @error('stato_civile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Data Variazione *</label>
                        <input type="date" name="data_variazione" value="{{ old('data_variazione', date('Y-m-d')) }}" required class="form-input w-full text-sm">
                        @error('data_variazione') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                        <input type="text" name="note" value="{{ old('note') }}" class="form-input w-full text-sm" placeholder="Note opzionali...">
                        @error('note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Registra Variazione
                    </button>
                    <button type="button" @click="showAddChange = false" class="text-sm text-gray-500 hover:text-gray-700">Annulla</button>
                </div>
            </form>
        </div>

        {{-- History Table --}}
        @if($member->familyStatusChanges->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Variazione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato Civile</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrato da</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($member->familyStatusChanges as $index => $change)
                    <tr class="{{ $index === 0 ? 'bg-blue-50/50' : '' }} hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $change->data_variazione->format('d/m/Y') }}
                            @if($index === 0)
                                <span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Attuale</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">{{ $change->stato_civile }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">{{ $change->note ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $change->registeredBy?->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h4 class="mt-3 text-sm font-medium text-gray-900">Nessuna variazione registrata</h4>
            <p class="mt-1 text-xs text-gray-500">Registra la prima variazione dello stato civile.</p>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- Nucleo Familiare Attivo --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">
                Nucleo Familiare
                @if($activeFamilyMembers->count() > 0)
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $activeFamilyMembers->count() }}</span>
                @endif
            </h3>
            @if(auth()->user()->hasPermission('stati_famiglia.manage'))
            <button @click="showAddMember = !showAddMember" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Aggiungi Componente
            </button>
            @endif
        </div>

        {{-- Add Family Member Form --}}
        <div x-show="showAddMember" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="POST" action="{{ route('family-status.store-family-member', $member) }}">
                @csrf
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Nuovo Componente Nucleo</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cognome *</label>
                        <input type="text" name="cognome" value="{{ old('cognome') }}" required class="form-input w-full text-sm">
                        @error('cognome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required class="form-input w-full text-sm">
                        @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Relazione *</label>
                        <select name="relazione" required class="form-select w-full text-sm">
                            <option value="">Seleziona...</option>
                            @foreach(['Coniuge', 'Figlio/a', 'Genitore', 'Fratello/Sorella', 'Convivente', 'Altro'] as $rel)
                            <option value="{{ $rel }}" {{ old('relazione') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                        @error('relazione') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}" class="form-input w-full text-sm font-mono" maxlength="16">
                        @error('codice_fiscale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Data di Nascita</label>
                        <input type="date" name="data_nascita" value="{{ old('data_nascita') }}" class="form-input w-full text-sm">
                        @error('data_nascita') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Luogo di Nascita</label>
                        <input type="text" name="luogo_nascita" value="{{ old('luogo_nascita') }}" class="form-input w-full text-sm">
                        @error('luogo_nascita') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Data Inizio (nel nucleo)</label>
                        <input type="date" name="data_inizio" value="{{ old('data_inizio') }}" class="form-input w-full text-sm">
                        @error('data_inizio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
                        Aggiungi Componente
                    </button>
                    <button type="button" @click="showAddMember = false" class="text-sm text-gray-500 hover:text-gray-700">Annulla</button>
                </div>
            </form>
        </div>

        {{-- Active Family Members --}}
        @if($activeFamilyMembers->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Relazione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Codice Fiscale</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data di Nascita</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nel nucleo da</th>
                        @if(auth()->user()->hasPermission('stati_famiglia.manage'))
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Azioni</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($activeFamilyMembers as $fm)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $fm->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700">{{ $fm->relazione }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">{{ $fm->codice_fiscale ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $fm->data_nascita ? $fm->data_nascita->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $fm->data_inizio ? $fm->data_inizio->format('d/m/Y') : '-' }}</td>
                        @if(auth()->user()->hasPermission('stati_famiglia.manage'))
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('family-status.destroy-family-member', $fm) }}" onsubmit="return confirm('Rimuovere questo componente dal nucleo familiare?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" title="Rimuovi dal nucleo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <h4 class="mt-3 text-sm font-medium text-gray-900">Nessun componente nel nucleo</h4>
            <p class="mt-1 text-xs text-gray-500">Aggiungi i componenti del nucleo familiare utilizzando il pulsante sopra.</p>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- Ex Componenti (rimossi dal nucleo) --}}
    {{-- ============================================================ --}}
    @if($inactiveFamilyMembers->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">
                Ex Componenti
                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $inactiveFamilyMembers->count() }}</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Relazione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periodo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inactiveFamilyMembers as $fm)
                    <tr class="hover:bg-gray-50 opacity-75">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $fm->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $fm->relazione }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $fm->data_inizio ? $fm->data_inizio->format('d/m/Y') : '?' }}
                            &rarr;
                            {{ $fm->data_fine ? $fm->data_fine->format('d/m/Y') : '?' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $fm->note ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
