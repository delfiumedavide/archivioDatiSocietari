@extends('layouts.app')
@section('title', 'Libri Sociali')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Libri Sociali</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Libri Sociali</h1>
            <p class="text-sm text-gray-500 mt-1">Verbali, delibere e presenze di CDA, Assemblee e Collegio Sindacale.</p>
        </div>
        <a href="{{ route('libri-sociali.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuova Riunione
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Dashboard cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Prossime riunioni (30 gg)</p>
            <p class="text-3xl font-bold text-brand-700 mt-1">{{ $upcomingCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Verbali mancanti</p>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-3xl font-bold {{ $missingVerbaleCount > 0 ? 'text-red-600' : 'text-gray-700' }}">{{ $missingVerbaleCount }}</p>
                @if($missingVerbaleCount > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Da caricare</span>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Riunioni {{ now()->year }}</p>
            <p class="text-3xl font-bold text-gray-700 mt-1">{{ $yearCount }}</p>
        </div>
    </div>

    {{-- Prossime riunioni --}}
    @if($upcoming->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Prossime Riunioni</h2>
            <span class="text-xs text-gray-400">Nei prossimi 30 giorni</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($upcoming as $r)
            <a href="{{ route('libri-sociali.show', $r) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full
                        @if($r->tipo === 'cda') bg-brand-500
                        @elseif($r->tipo === 'collegio_sindacale') bg-purple-500
                        @elseif($r->tipo === 'assemblea_ordinaria') bg-teal-500
                        @else bg-orange-500 @endif">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $r->tipo_short }}</p>
                        <p class="text-xs text-gray-500">{{ $r->company->denominazione }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-700">{{ $r->data_ora->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400">ore {{ $r->data_ora->format('H:i') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Verbali mancanti --}}
    @if($missingVerbale->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h2 class="text-base font-semibold text-red-700">Verbali da Caricare</h2>
        </div>
        <div class="divide-y divide-red-50">
            @foreach($missingVerbale as $r)
            <a href="{{ route('libri-sociali.show', $r) }}" class="flex items-center justify-between px-6 py-3 hover:bg-red-50 transition-colors">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $r->tipo_short }} — {{ $r->company->denominazione }}</p>
                    <p class="text-xs text-gray-500">{{ $r->data_ora->format('d/m/Y') }}</p>
                </div>
                <span class="text-xs text-red-600 font-medium">Carica verbale →</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtri + lista completa --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Tutte le Riunioni</h2>
        </div>

        {{-- Filtri --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('libri-sociali.index') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <select name="tipo" class="form-select">
                    <option value="">Tutti i tipi</option>
                    <option value="cda" {{ request('tipo') === 'cda' ? 'selected' : '' }}>CDA</option>
                    <option value="collegio_sindacale" {{ request('tipo') === 'collegio_sindacale' ? 'selected' : '' }}>Collegio Sindacale</option>
                    <option value="assemblea_ordinaria" {{ request('tipo') === 'assemblea_ordinaria' ? 'selected' : '' }}>Assemblea Ordinaria</option>
                    <option value="assemblea_straordinaria" {{ request('tipo') === 'assemblea_straordinaria' ? 'selected' : '' }}>Assemblea Straordinaria</option>
                </select>
                <select name="company_id" class="form-select">
                    <option value="">Tutte le societa</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->denominazione }}</option>
                    @endforeach
                </select>
                <select name="anno" class="form-select">
                    <option value="">Tutti gli anni</option>
                    @foreach($anni as $anno)
                    <option value="{{ $anno }}" {{ request('anno') == $anno ? 'selected' : '' }}>{{ $anno }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="programmata" {{ request('status') === 'programmata' ? 'selected' : '' }}>Programmata</option>
                    <option value="convocata" {{ request('status') === 'convocata' ? 'selected' : '' }}>Convocata</option>
                    <option value="svolta" {{ request('status') === 'svolta' ? 'selected' : '' }}>Svolta</option>
                    <option value="annullata" {{ request('status') === 'annullata' ? 'selected' : '' }}>Annullata</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtra</button>
                    <a href="{{ route('libri-sociali.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        {{-- Tabella --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Societa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stato</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Conv.</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Verbale</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riunioni as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full flex-shrink-0
                                    @if($r->tipo === 'cda') bg-brand-500
                                    @elseif($r->tipo === 'collegio_sindacale') bg-purple-500
                                    @elseif($r->tipo === 'assemblea_ordinaria') bg-teal-500
                                    @else bg-orange-500 @endif">
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $r->tipo_short }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $r->company->denominazione }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $r->data_ora->format('d/m/Y') }}
                            <span class="text-xs text-gray-400 block">ore {{ $r->data_ora->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php $color = $r->status_color @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($color === 'green') bg-green-100 text-green-800
                                @elseif($color === 'blue') bg-blue-100 text-blue-800
                                @elseif($color === 'red') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $r->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($r->has_convocazione)
                            <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg class="w-4 h-4 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($r->has_verbale)
                            <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg class="w-4 h-4 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('libri-sociali.show', $r) }}" class="text-brand-600 hover:underline text-sm font-medium">Dettaglio</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                            Nessuna riunione trovata.
                            <a href="{{ route('libri-sociali.create') }}" class="text-brand-600 hover:underline ml-1">Crea la prima →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riunioni->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $riunioni->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
