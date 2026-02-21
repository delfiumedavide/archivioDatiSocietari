@extends('layouts.app')

@section('title', 'Dichiarazioni Stato Famiglia')

@section('breadcrumb')
    <span class="text-gray-400 font-light text-base select-none">&rsaquo;</span>
    <a href="{{ route('family-status.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors font-medium">Stati Famiglia</a>
    <span class="text-gray-400 font-light text-base select-none">&rsaquo;</span>
    <span class="text-gray-700 font-medium">Dichiarazioni {{ $year }}</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dichiarazioni Stato di Famiglia</h1>
            <p class="mt-1 text-sm text-gray-500">Genera, scarica e gestisci le dichiarazioni annuali</p>
        </div>
        <a href="{{ route('family-status.index') }}" class="inline-flex items-center gap-2 text-sm text-brand-600 hover:text-brand-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Torna a Stati Famiglia
        </a>
    </div>

    {{-- Year Selector & Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Year selector --}}
            <form method="GET" action="{{ route('family-status.declarations') }}" class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Anno:</label>
                <select name="anno" onchange="this.form.submit()" class="form-select text-sm w-28">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @if($search)
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                @if($filterSigned)
                    <input type="hidden" name="firma" value="{{ $filterSigned }}">
                @endif
            </form>

            {{-- Bulk Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('family-status.declarations.bulk-generate') }}" onsubmit="return confirm('Generare le dichiarazioni per tutti i membri attivi per il {{ $year }}?')">
                    @csrf
                    <input type="hidden" name="anno" value="{{ $year }}">
                    <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-2 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Genera Tutte {{ $year }}
                    </button>
                </form>

                @if($stats['generate'] > 0)
                <a href="{{ route('family-status.declarations.bulk-download', ['anno' => $year]) }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-3 py-2 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Scarica ZIP {{ $year }}
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['totale'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Membri Attivi</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-2xl font-bold text-indigo-600">{{ $stats['generate'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Dichiarazioni Generate</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['firmate'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Firmate</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-2xl font-bold {{ $stats['da_firmare'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ $stats['da_firmare'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Da Firmare</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('family-status.declarations') }}" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="anno" value="{{ $year }}">

            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cerca per nome, cognome, codice fiscale..." class="form-input w-full pl-10 text-sm">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="sm:w-48">
                <select name="firma" class="form-select w-full text-sm">
                    <option value="">Tutti</option>
                    <option value="firmati" {{ $filterSigned === 'firmati' ? 'selected' : '' }}>Firmati</option>
                    <option value="non_firmati" {{ $filterSigned === 'non_firmati' ? 'selected' : '' }}>Da Firmare</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtra
                </button>
                @if($search || $filterSigned)
                <a href="{{ route('family-status.declarations', ['anno' => $year]) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 px-3 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Azzera
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Table --}}
    @if($members->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Membro</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Codice Fiscale</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato Civile</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Dichiarazione</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Firma</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($members as $member)
                    @php
                        $declaration = $declarations->get($member->id);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('family-status.show', $member) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600 transition">
                                {{ $member->cognome }} {{ $member->nome }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-700 font-mono">{{ $member->codice_fiscale ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($declaration?->stato_civile)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">{{ $declaration->stato_civile }}</span>
                            @elseif($member->current_stato_civile)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $member->current_stato_civile }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($declaration?->is_generated)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Generata
                                </span>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $declaration->generated_at->format('d/m/Y') }}</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Non generata</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($declaration?->is_signed)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Firmata
                                </span>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $declaration->signed_at->format('d/m/Y') }}</div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">Da firmare</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1" x-data="{ showUpload: false }">
                                {{-- Generate --}}
                                <form method="POST" action="{{ route('family-status.declarations.generate', $member) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="anno" value="{{ $year }}">
                                    <button type="submit" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-700 font-medium px-2 py-1 rounded hover:bg-indigo-50 transition" title="{{ $declaration?->is_generated ? 'Rigenera' : 'Genera' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ $declaration?->is_generated ? 'Rigenera' : 'Genera' }}
                                    </button>
                                </form>

                                @if($declaration?->is_generated)
                                    {{-- Download generated --}}
                                    <a href="{{ route('family-status.declarations.download', $declaration) }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-700 font-medium px-2 py-1 rounded hover:bg-gray-100 transition" title="Scarica PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>

                                    {{-- Upload signed toggle --}}
                                    <button @click="showUpload = !showUpload" class="inline-flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 font-medium px-2 py-1 rounded hover:bg-amber-50 transition" title="Carica firmata">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </button>

                                    @if($declaration->is_signed)
                                        {{-- Download signed --}}
                                        <a href="{{ route('family-status.declarations.download-signed', $declaration) }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium px-2 py-1 rounded hover:bg-green-50 transition" title="Scarica firmata">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </a>
                                    @endif
                                @endif

                                {{-- Upload form (shown on toggle) --}}
                                <div x-show="showUpload" x-transition @click.outside="showUpload = false" class="absolute right-0 mt-40 z-10 bg-white rounded-lg shadow-lg border border-gray-200 p-4 w-72">
                                    @if($declaration)
                                    <form method="POST" action="{{ route('family-status.declarations.upload-signed', $declaration) }}" enctype="multipart/form-data">
                                        @csrf
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Carica Dichiarazione Firmata</label>
                                        <input type="file" name="signed_file" accept=".pdf,.p7m" required class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                        <p class="text-xs text-gray-400 mt-1">PDF o P7M, max 50MB</p>
                                        <button type="submit" class="mt-2 w-full inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-3 py-1.5 rounded-lg transition text-sm">
                                            Carica
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900">Nessun risultato</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if($search || $filterSigned)
                    Prova a modificare i filtri di ricerca.
                @else
                    Non ci sono membri attivi registrati.
                @endif
            </p>
        </div>
    @endif

</div>
@endsection
