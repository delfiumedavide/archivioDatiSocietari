@extends('layouts.app')

@section('title', 'Societa')

@section('breadcrumb')
    <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Societa</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Societa</h1>
            <p class="mt-1 text-sm text-gray-500">Gestione dell'archivio societario</p>
        </div>
        @if(auth()->user()->hasPermission('companies.create'))
        <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuova Societa
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('companies.index') }}" class="flex flex-col sm:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca per denominazione, P.IVA, CF..." class="form-input w-full pl-10 text-sm">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            {{-- Forma giuridica filter --}}
            <div class="sm:w-48">
                <select name="forma_giuridica" class="form-select w-full text-sm">
                    <option value="">Tutte le forme</option>
                    <option value="SRL" {{ request('forma_giuridica') === 'SRL' ? 'selected' : '' }}>SRL</option>
                    <option value="SPA" {{ request('forma_giuridica') === 'SPA' ? 'selected' : '' }}>SPA</option>
                    <option value="SAS" {{ request('forma_giuridica') === 'SAS' ? 'selected' : '' }}>SAS</option>
                    <option value="SNC" {{ request('forma_giuridica') === 'SNC' ? 'selected' : '' }}>SNC</option>
                    <option value="Cooperativa" {{ request('forma_giuridica') === 'Cooperativa' ? 'selected' : '' }}>Cooperativa</option>
                    <option value="Altro" {{ request('forma_giuridica') === 'Altro' ? 'selected' : '' }}>Altro</option>
                </select>
            </div>

            {{-- Status filter --}}
            <div class="sm:w-40">
                <select name="status" class="form-select w-full text-sm">
                    <option value="">Tutti gli stati</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Attive</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inattive</option>
                </select>
            </div>

            {{-- Filter actions --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtra
                </button>
                @if(request()->hasAny(['search', 'forma_giuridica', 'status']))
                <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 px-3 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Azzera
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($companies->count() > 0)
        {{-- Card Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($companies as $company)
            <a href="{{ route('companies.show', $company) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-brand-300 transition group">
                <div class="p-5">
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-brand-600 transition leading-tight">
                            {{ $company->denominazione }}
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $company->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $company->is_active ? 'Attiva' : 'Inattiva' }}
                        </span>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($company->partita_iva)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                            <span class="font-semibold">P.IVA</span> {{ $company->partita_iva }}
                        </span>
                        @endif
                        @if($company->codice_fiscale)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-xs font-medium">
                            <span class="font-semibold">CF</span> {{ $company->codice_fiscale }}
                        </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="space-y-2 text-sm text-gray-600">
                        @if($company->forma_giuridica)
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 bg-gold-100 text-gold-800 rounded text-xs font-semibold">
                                {{ $company->forma_giuridica }}
                            </span>
                        </div>
                        @endif

                        @if($company->sede_legale_citta)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $company->sede_legale_citta }}{{ $company->sede_legale_provincia ? ' (' . $company->sede_legale_provincia . ')' : '' }}</span>
                        </div>
                        @endif

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>{{ $company->documents_count ?? 0 }} documenti</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $companies->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900">Nessuna societa trovata</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'forma_giuridica', 'status']))
                    Prova a modificare i filtri di ricerca.
                @else
                    Non ci sono ancora societa registrate nell'archivio.
                @endif
            </p>
            @if(auth()->user()->hasPermission('companies.create'))
            <div class="mt-6">
                <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-5 py-2.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Aggiungi la prima societa
                </a>
            </div>
            @endif
        </div>
    @endif

</div>
@endsection
