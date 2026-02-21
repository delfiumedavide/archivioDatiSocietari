@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('dashboard') }}" class="text-brand-600 font-medium">Home</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Page Title --}}
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Buongiorno' : ($hour < 18 ? 'Buon pomeriggio' : 'Buonasera');
        $userName = auth()->user()->name;
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $greeting }}, <span class="text-brand-600">{{ $userName }}</span> 👋
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                @if(auth()->user()->isAdmin())
                    Ecco il riepilogo completo dell'archivio societario &mdash; {{ now()->translatedFormat('l d F Y') }}
                @elseif($stats['total_companies'] === 1)
                    Ecco la gestione della tua società &mdash; {{ now()->translatedFormat('l d F Y') }}
                @else
                    Ecco la gestione delle tue {{ $stats['total_companies'] }} società &mdash; {{ now()->translatedFormat('l d F Y') }}
                @endif
            </p>
        </div>
        <div class="text-sm text-gray-400 hidden sm:block">
            <svg class="w-5 h-5 inline-block mr-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ now()->translatedFormat('H:i') }}
        </div>
    </div>

    {{-- ── ROW 1: Stat Cards principali ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Societa --}}
        <a href="{{ route('companies.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-brand-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Societa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_companies']) }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- Membri --}}
        <a href="{{ route('members.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-brand-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Soci Attivi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_members']) }}</p>
                </div>
                <div class="w-11 h-11 bg-teal-50 rounded-lg flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- Documenti --}}
        <a href="{{ route('documents.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-brand-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Documenti</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_documents']) }}</p>
                </div>
                <div class="w-11 h-11 bg-purple-50 rounded-lg flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </a>

        @if(auth()->user()->isAdmin())
        {{-- Riunioni Anno --}}
        <a href="{{ route('libri-sociali.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-brand-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Riunioni {{ now()->year }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($riunioniStats['year']) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $riunioniStats['upcoming'] }} imminenti</p>
                </div>
                <div class="w-11 h-11 bg-brand-50 rounded-lg flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </a>
        @else
        {{-- Documenti in Scadenza (per non-admin) --}}
        <a href="{{ route('documents.expiring') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-yellow-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">In Scadenza</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['expiring_count']) }}</p>
                </div>
                <div class="w-11 h-11 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>
        @endif
    </div>

    {{-- ── ROW 2: Alert Cards (attenzione richiesta) ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Docs in Scadenza --}}
        <a href="{{ route('documents.expiring') }}"
           class="rounded-xl border p-4 flex items-center gap-4 hover:shadow-md transition-all
                  {{ $stats['expiring_count'] > 0 ? 'bg-yellow-50 border-yellow-200' : 'bg-white border-gray-200' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $stats['expiring_count'] > 0 ? 'bg-yellow-100' : 'bg-gray-100' }}">
                <svg class="w-5 h-5 {{ $stats['expiring_count'] > 0 ? 'text-yellow-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $stats['expiring_count'] > 0 ? 'text-yellow-700' : 'text-gray-400' }}">{{ $stats['expiring_count'] }}</p>
                <p class="text-xs font-medium {{ $stats['expiring_count'] > 0 ? 'text-yellow-600' : 'text-gray-400' }}">Docs in scadenza</p>
            </div>
        </a>

        {{-- Docs Scaduti --}}
        <a href="{{ route('documents.expiring') }}"
           class="rounded-xl border p-4 flex items-center gap-4 hover:shadow-md transition-all
                  {{ $stats['expired_count'] > 0 ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $stats['expired_count'] > 0 ? 'bg-red-100' : 'bg-gray-100' }}">
                <svg class="w-5 h-5 {{ $stats['expired_count'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $stats['expired_count'] > 0 ? 'text-red-700' : 'text-gray-400' }}">{{ $stats['expired_count'] }}</p>
                <p class="text-xs font-medium {{ $stats['expired_count'] > 0 ? 'text-red-600' : 'text-gray-400' }}">Documenti scaduti</p>
            </div>
        </a>

        @if(auth()->user()->isAdmin())
        {{-- Verbali Mancanti --}}
        <a href="{{ route('libri-sociali.index') }}"
           class="rounded-xl border p-4 flex items-center gap-4 hover:shadow-md transition-all
                  {{ $riunioniStats['missing_verbale'] > 0 ? 'bg-orange-50 border-orange-200' : 'bg-white border-gray-200' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $riunioniStats['missing_verbale'] > 0 ? 'bg-orange-100' : 'bg-gray-100' }}">
                <svg class="w-5 h-5 {{ $riunioniStats['missing_verbale'] > 0 ? 'text-orange-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $riunioniStats['missing_verbale'] > 0 ? 'text-orange-700' : 'text-gray-400' }}">{{ $riunioniStats['missing_verbale'] }}</p>
                <p class="text-xs font-medium {{ $riunioniStats['missing_verbale'] > 0 ? 'text-orange-600' : 'text-gray-400' }}">Verbali mancanti</p>
            </div>
        </a>

        {{-- Dichiarazioni non firmate --}}
        <a href="{{ route('family-status.declarations') }}"
           class="rounded-xl border p-4 flex items-center gap-4 hover:shadow-md transition-all
                  {{ $declarationStats['unsigned'] > 0 ? 'bg-amber-50 border-amber-200' : 'bg-white border-gray-200' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $declarationStats['unsigned'] > 0 ? 'bg-amber-100' : 'bg-gray-100' }}">
                <svg class="w-5 h-5 {{ $declarationStats['unsigned'] > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $declarationStats['unsigned'] > 0 ? 'text-amber-700' : 'text-gray-400' }}">{{ $declarationStats['unsigned'] }}</p>
                <p class="text-xs font-medium {{ $declarationStats['unsigned'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">Dichiarazioni da firmare</p>
            </div>
        </a>
        @else
        {{-- Placeholder per non-admin: Scaduti placeholder (già mostrato sopra) --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_documents'] }}</p>
                <p class="text-xs font-medium text-gray-500">Totale documenti</p>
            </div>
        </div>
        <div></div>
        @endif
    </div>

    {{-- ── LIBRI SOCIALI WIDGET (solo admin) ── --}}
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Prossime Riunioni --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-base font-semibold text-gray-900">Prossime Riunioni</h2>
                </div>
                <a href="{{ route('libri-sociali.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                    Vedi tutte
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if($prossimeRiunioni->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Nessuna riunione programmata</p>
                    <a href="{{ route('libri-sociali.create') }}" class="mt-2 inline-block text-xs text-brand-600 hover:underline">+ Nuova riunione</a>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($prossimeRiunioni as $r)
                    <a href="{{ route('libri-sociali.show', $r) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                        {{-- Tipo dot --}}
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0
                            @if($r->tipo === 'cda') bg-brand-600
                            @elseif($r->tipo === 'collegio_sindacale') bg-purple-500
                            @elseif($r->tipo === 'assemblea_ordinaria') bg-teal-500
                            @else bg-orange-500 @endif"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $r->tipo_short }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $r->company->denominazione }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $r->data_ora->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $r->data_ora->format('H:i') }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            bg-{{ $r->status_color }}-100 text-{{ $r->status_color }}-700">
                            {{ $r->status_label }}
                        </span>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Verbali da Caricare --}}
        <div class="bg-white rounded-xl shadow-sm border {{ $missingVerbali->isNotEmpty() ? 'border-orange-200' : 'border-gray-200' }} overflow-hidden">
            <div class="px-6 py-4 border-b {{ $missingVerbali->isNotEmpty() ? 'border-orange-200 bg-orange-50' : 'border-gray-200' }} flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 {{ $missingVerbali->isNotEmpty() ? 'text-orange-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-base font-semibold {{ $missingVerbali->isNotEmpty() ? 'text-orange-800' : 'text-gray-900' }}">Verbali da Caricare</h2>
                    @if($missingVerbali->isNotEmpty())
                    <span class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $riunioniStats['missing_verbale'] }}</span>
                    @endif
                </div>
                <a href="{{ route('libri-sociali.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                    Gestisci
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if($missingVerbali->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Tutti i verbali sono stati caricati</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($missingVerbali as $r)
                    <a href="{{ route('libri-sociali.show', $r) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-orange-50 transition-colors">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 bg-orange-400"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $r->tipo_short }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $r->company->denominazione }}</p>
                        </div>
                        <p class="text-sm text-gray-500 flex-shrink-0">{{ $r->data_ora->format('d/m/Y') }}</p>
                        <span class="text-xs text-orange-600 font-medium flex-shrink-0">Verbale mancante</span>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
    @endif

    {{-- ── Dichiarazioni Anno Corrente (solo admin) ── --}}
    @if(auth()->user()->isAdmin() && ($declarationStats['generated'] > 0 || $declarationStats['unsigned'] > 0))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <h2 class="text-base font-semibold text-gray-900">Dichiarazioni Stato di Famiglia &mdash; {{ now()->year }}</h2>
            </div>
            <a href="{{ route('family-status.declarations') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                Gestisci
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="flex items-center gap-8">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $declarationStats['generated'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Generate</p>
            </div>
            <div class="h-10 w-px bg-gray-200"></div>
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">{{ $declarationStats['signed'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Firmate</p>
            </div>
            <div class="h-10 w-px bg-gray-200"></div>
            <div class="text-center">
                <p class="text-3xl font-bold {{ $declarationStats['unsigned'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ $declarationStats['unsigned'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Da firmare</p>
            </div>
            @if($declarationStats['generated'] > 0)
            <div class="flex-1 ml-4">
                @php $pct = $declarationStats['generated'] > 0 ? round($declarationStats['signed'] / $declarationStats['generated'] * 100) : 0 @endphp
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                    <span>Completamento</span>
                    <span class="font-semibold">{{ $pct }}%</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $pct === 100 ? 'bg-green-500' : 'bg-amber-400' }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Documenti in Scadenza ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Documenti in Scadenza</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ordinati per urgenza</p>
            </div>
            <a href="{{ route('documents.expiring') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                Vedi tutti
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Societa</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Scadenza</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expiringDocuments as $document)
                        <tr class="{{ $document->computed_status === 'expired' ? 'bg-red-50' : ($document->computed_status === 'expiring' ? 'bg-yellow-50' : '') }} hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                <a href="{{ route('documents.show', $document) }}" class="text-brand-600 hover:text-brand-700 font-medium">
                                    {{ $document->title }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $document->company?->denominazione ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $document->category?->label ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $document->expiration_date?->format('d/m/Y') ?? '-' }}
                                <span class="block text-xs {{ $document->days_until_expiration < 0 ? 'text-red-500' : 'text-yellow-600' }}">
                                    @if($document->days_until_expiration < 0)
                                        Scaduto da {{ abs($document->days_until_expiration) }} giorni
                                    @elseif($document->days_until_expiration === 0)
                                        Scade oggi
                                    @else
                                        Tra {{ $document->days_until_expiration }} giorni
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $document->computed_status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $document->computed_status === 'expiring' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $document->computed_status === 'valid' ? 'bg-green-100 text-green-800' : '' }}
                                ">
                                    {{ $document->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="font-medium">Nessun documento in scadenza</p>
                                <p class="text-sm mt-1">Tutti i documenti sono in regola.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Charts Grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Documenti per Categoria</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartByCategory"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Documenti per Societa</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartByCompany"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Stato Scadenze</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartExpirationStatus"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Caricamenti (12 mesi)</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartUploadActivity"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Recent Activity ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Attivita Recenti</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ultime operazioni effettuate</p>
            </div>
            <a href="{{ route('activity-log.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                Vedi tutte
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentActivity as $activity)
                <div class="px-6 py-3 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-2 h-2 rounded-full flex-shrink-0 bg-{{ $activity->action_color }}-500"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">{{ $activity->user?->name ?? 'Sistema' }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-{{ $activity->action_color }}-100 text-{{ $activity->action_color }}-700 mx-1">
                                {{ $activity->action_label }}
                            </span>
                            <span class="text-gray-600">{{ $activity->description }}</span>
                        </p>
                    </div>
                    <time class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap">
                        {{ $activity->created_at->diffForHumans() }}
                    </time>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="font-medium">Nessuna attivita recente</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const Chart = window.Chart;
    if (!Chart) return;

    const brandPrimary = '#1e3a5f';
    const brandGold = '#c9952b';
    const brandPalette = [
        '#1e3a5f', '#c9952b', '#2563eb', '#7c3aed', '#059669',
        '#dc2626', '#d97706', '#0891b2', '#4f46e5', '#be185d',
        '#65a30d', '#ea580c'
    ];

    const defaultFont = { family: "'Inter', 'Segoe UI', sans-serif" };
    Chart.defaults.font = defaultFont;
    Chart.defaults.color = '#6b7280';

    const categoryData = @json($documentsByCategory);
    new Chart(document.getElementById('chartByCategory'), {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.data,
                backgroundColor: brandPalette.slice(0, categoryData.labels.length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '60%',
            plugins: { legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, pointStyleWidth: 10 } } }
        }
    });

    const companyData = @json($documentsByCompany);
    new Chart(document.getElementById('chartByCompany'), {
        type: 'bar',
        data: {
            labels: companyData.labels,
            datasets: [{
                label: 'Documenti',
                data: companyData.data,
                backgroundColor: brandPrimary + 'cc',
                borderColor: brandPrimary,
                borderWidth: 1, borderRadius: 6, barPercentage: 0.7
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0, font: { size: 11 } } }
            }
        }
    });

    const expirationData = @json($expirationData);
    new Chart(document.getElementById('chartExpirationStatus'), {
        type: 'pie',
        data: {
            labels: expirationData.labels,
            datasets: [{
                data: expirationData.data,
                backgroundColor: ['#059669', '#d97706', '#dc2626'],
                borderWidth: 2, borderColor: '#ffffff', hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, pointStyleWidth: 10 } } }
        }
    });

    const uploadData = @json($uploadActivity);
    new Chart(document.getElementById('chartUploadActivity'), {
        type: 'line',
        data: {
            labels: uploadData.labels,
            datasets: [{
                label: 'Documenti caricati',
                data: uploadData.data,
                borderColor: brandGold,
                backgroundColor: brandGold + '20',
                fill: true, tension: 0.4, borderWidth: 2.5,
                pointBackgroundColor: brandGold, pointBorderColor: '#ffffff',
                pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
});
</script>
@endsection
