@extends('layouts.app')
@section('title', 'Scadenzario')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Scadenzario</span>
@endsection

@section('content')
@php
    $docsCount    = $expired->count() + $expiring->count();
    $offCount     = $expiredOfficers->count() + $expiringOfficers->count();
    $wlCount      = $expiredWhiteList->count() + $expiringWhiteList->count();
@endphp
<div class="space-y-6" x-data="{ activeTab: 'documenti' }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Scadenzario</h1>
            <p class="text-sm text-gray-500 mt-1">Documenti, cariche societarie e white list in scadenza o già scaduti.</p>
        </div>
    </div>

    {{-- Tab Bar --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex gap-1">
            <button @click="activeTab = 'documenti'"
                    :class="activeTab === 'documenti' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documenti
                @if($docsCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full {{ $expired->isNotEmpty() ? 'bg-red-500 text-white' : 'bg-yellow-400 text-yellow-900' }}">{{ $docsCount }}</span>
                @endif
            </button>

            <button @click="activeTab = 'cariche'"
                    :class="activeTab === 'cariche' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Cariche Societarie
                @if($offCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full {{ $expiredOfficers->isNotEmpty() ? 'bg-red-500 text-white' : 'bg-yellow-400 text-yellow-900' }}">{{ $offCount }}</span>
                @endif
            </button>

            <button @click="activeTab = 'whitelist'"
                    :class="activeTab === 'whitelist' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                White List
                @if($wlCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full {{ $expiredWhiteList->isNotEmpty() ? 'bg-red-500 text-white' : 'bg-yellow-400 text-yellow-900' }}">{{ $wlCount }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- TAB: DOCUMENTI --}}
    <div x-show="activeTab === 'documenti'" class="space-y-6">
        @if($expired->isNotEmpty())
        <div class="card border-red-200">
            <div class="card-header bg-red-50 border-red-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-red-800">Documenti Scaduti ({{ $expired->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Associato a</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scaduto il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expired as $doc)
                        <tr class="bg-red-50/50">
                            <td class="px-6 py-3"><a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium text-brand-900 hover:underline">{{ $doc->title }}</a></td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $doc->owner_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $doc->category?->label ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-red-700 font-medium">{{ $doc->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-red">{{ abs($doc->days_until_expiration) }} gg fa</span></td>
                            <td class="px-6 py-3 text-right"><a href="{{ route('documents.show', $doc) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expiring->isNotEmpty())
        <div class="card border-yellow-200">
            <div class="card-header bg-yellow-50 border-yellow-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-yellow-800">In Scadenza ({{ $expiring->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Associato a</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scade il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiring as $doc)
                        <tr class="bg-yellow-50/50">
                            <td class="px-6 py-3"><a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium text-brand-900 hover:underline">{{ $doc->title }}</a></td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $doc->owner_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $doc->category?->label ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-yellow-700 font-medium">{{ $doc->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-yellow">{{ $doc->days_until_expiration }} gg</span></td>
                            <td class="px-6 py-3 text-right"><a href="{{ route('documents.show', $doc) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expired->isEmpty() && $expiring->isEmpty())
        <div class="card">
            <div class="card-body text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900">Tutto in ordine!</h3>
                <p class="text-gray-500 mt-1">Non ci sono documenti in scadenza o scaduti.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- TAB: CARICHE SOCIETARIE --}}
    <div x-show="activeTab === 'cariche'" class="space-y-6">

        @if($expiredOfficers->isNotEmpty())
        <div class="card border-red-200">
            <div class="card-header bg-red-50 border-red-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-red-800">Cariche Scadute ({{ $expiredOfficers->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Membro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ruolo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Società</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scaduta il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni fa</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiredOfficers as $officer)
                        <tr class="bg-red-50/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $officer->member?->full_name ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $officer->ruolo }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $officer->company?->denominazione ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-red-700 font-medium">{{ $officer->data_scadenza?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-red">{{ $officer->data_scadenza?->diffInDays(now()) }} gg fa</span></td>
                            <td class="px-6 py-3 text-right">
                                @if($officer->company)
                                <a href="{{ route('companies.show', $officer->company) }}#cariche" class="text-brand-600 hover:underline text-sm">Gestisci</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expiringOfficers->isNotEmpty())
        <div class="card border-yellow-200">
            <div class="card-header bg-yellow-50 border-yellow-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-yellow-800">Cariche in Scadenza — prossimi 90 gg ({{ $expiringOfficers->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Membro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ruolo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Società</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scade il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiringOfficers as $officer)
                        <tr class="bg-yellow-50/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $officer->member?->full_name ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $officer->ruolo }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $officer->company?->denominazione ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-yellow-700 font-medium">{{ $officer->data_scadenza?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-yellow">{{ now()->diffInDays($officer->data_scadenza) }} gg</span></td>
                            <td class="px-6 py-3 text-right">
                                @if($officer->company)
                                <a href="{{ route('companies.show', $officer->company) }}#cariche" class="text-brand-600 hover:underline text-sm">Gestisci</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expiredOfficers->isEmpty() && $expiringOfficers->isEmpty())
        <div class="card">
            <div class="card-body text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900">Nessuna carica in scadenza</h3>
                <p class="text-gray-500 mt-1">Tutte le cariche societarie sono aggiornate nei prossimi 90 giorni.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- TAB: WHITE LIST --}}
    <div x-show="activeTab === 'whitelist'" class="space-y-6">

        @if($expiredWhiteList->isNotEmpty())
        <div class="card border-red-200">
            <div class="card-header bg-red-50 border-red-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-red-800">White List Scadute ({{ $expiredWhiteList->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Membro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Codice Fiscale</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scaduta il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni fa</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiredWhiteList as $member)
                        <tr class="bg-red-50/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $member->full_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 font-mono">{{ $member->codice_fiscale ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-red-700 font-medium">{{ $member->white_list_scadenza?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-red">{{ $member->white_list_scadenza?->diffInDays(now()) }} gg fa</span></td>
                            <td class="px-6 py-3 text-right"><a href="{{ route('members.show', $member) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expiringWhiteList->isNotEmpty())
        <div class="card border-yellow-200">
            <div class="card-header bg-yellow-50 border-yellow-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <h2 class="text-base font-semibold text-yellow-800">White List in Scadenza — prossimi 90 gg ({{ $expiringWhiteList->count() }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Membro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Codice Fiscale</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scade il</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiringWhiteList as $member)
                        <tr class="bg-yellow-50/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $member->full_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 font-mono">{{ $member->codice_fiscale ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-yellow-700 font-medium">{{ $member->white_list_scadenza?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="badge-yellow">{{ now()->diffInDays($member->white_list_scadenza) }} gg</span></td>
                            <td class="px-6 py-3 text-right"><a href="{{ route('members.show', $member) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($expiredWhiteList->isEmpty() && $expiringWhiteList->isEmpty())
        <div class="card">
            <div class="card-body text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900">Nessuna white list in scadenza</h3>
                <p class="text-gray-500 mt-1">Tutte le white list sono aggiornate nei prossimi 90 giorni.</p>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
