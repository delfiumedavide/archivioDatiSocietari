@extends('layouts.app')
@section('title', 'Documenti')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Documenti</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Archivio Documenti</h1>
        @if(auth()->user()->hasPermission('documents.upload'))
        <a href="{{ route('documents.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Carica Documento
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca per titolo..." class="form-input">
                </div>
                <div>
                    <select name="company_id" class="form-select">
                        <option value="">Tutte le societa</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->denominazione }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="category_id" class="form-select">
                        <option value="">Tutte le categorie</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" class="form-select">
                        <option value="">Tutti gli stati</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Validi</option>
                        <option value="expiring" {{ request('status') === 'expiring' ? 'selected' : '' }}>In Scadenza</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Scaduti</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtra</button>
                    <a href="{{ route('documents.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Societa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Scadenza</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dimensione</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $document)
                    <tr class="hover:bg-gray-50 {{ $document->computed_status === 'expired' ? 'bg-red-50' : ($document->computed_status === 'expiring' ? 'bg-yellow-50' : '') }}">
                        <td class="px-6 py-4">
                            <a href="{{ route('documents.show', $document) }}" class="text-sm font-medium text-brand-900 hover:underline">{{ $document->title }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">v{{ $document->current_version }} &middot; {{ $document->file_name_original }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $document->company->denominazione }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-100 text-brand-800">{{ $document->category->label }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $document->expiration_date?->format('d/m/Y') ?? '-' }}
                            @if($document->days_until_expiration !== null)
                            <span class="text-xs text-gray-500">({{ $document->days_until_expiration }} gg)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge-{{ $document->status_color }}">{{ $document->status_label }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $document->file_size_formatted }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('documents.download', $document) }}" class="text-brand-600 hover:text-brand-800" title="Scarica">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            <a href="{{ route('documents.show', $document) }}" class="text-gray-500 hover:text-gray-700" title="Dettagli">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-lg font-medium">Nessun documento trovato</p>
                            <p class="text-sm mt-1">Carica il primo documento per iniziare.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
