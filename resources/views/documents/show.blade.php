@extends('layouts.app')
@section('title', $document->title)
@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('documents.index') }}" class="text-brand-600 hover:underline">Documenti</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">{{ Str::limit($document->title, 30) }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="badge-{{ $document->status_color }}">{{ $document->status_label }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-100 text-brand-800">{{ $document->category?->label ?? '-' }}</span>
                <span class="text-sm text-gray-500">v{{ $document->current_version }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('documents.download', $document) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Scarica
            </a>
            @if(auth()->user()->hasPermission('documents.delete'))
            <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Eliminare questo documento?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Elimina</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Document Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="card-header"><h2 class="text-lg font-semibold">Dettagli Documento</h2></div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Societa</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                @if($document->company)
                                    <a href="{{ route('companies.show', $document->company) }}" class="text-brand-600 hover:underline">{{ $document->company->denominazione }}</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Categoria</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $document->category?->label ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">File Originale</dt>
                            <dd class="text-sm font-medium text-gray-900 break-all">{{ $document->file_name_original }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dimensione</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $document->file_size_formatted }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Tipo File</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ strtoupper(pathinfo($document->file_name_original, PATHINFO_EXTENSION)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Versione</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $document->current_version }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Caricato da</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $document->uploader->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Data Caricamento</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $document->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @if($document->expiration_date)
                        <div>
                            <dt class="text-sm text-gray-500">Data Scadenza</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $document->expiration_date->format('d/m/Y') }}
                                @if($document->days_until_expiration !== null)
                                <span class="badge-{{ $document->status_color }} ml-2">{{ $document->days_until_expiration > 0 ? $document->days_until_expiration . ' giorni' : 'Scaduto' }}</span>
                                @endif
                            </dd>
                        </div>
                        @endif
                    </dl>
                    @if($document->description)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <dt class="text-sm text-gray-500 mb-1">Descrizione</dt>
                        <dd class="text-sm text-gray-700">{{ $document->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Version History --}}
            @if($document->versions->isNotEmpty())
            <div class="card">
                <div class="card-header"><h2 class="text-lg font-semibold">Storico Versioni</h2></div>
                <div class="card-body p-0">
                    <div class="divide-y divide-gray-200">
                        @foreach($document->versions as $version)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Versione {{ $version->version }}</p>
                                <p class="text-xs text-gray-500">{{ $version->uploader->name }} - {{ $version->created_at->format('d/m/Y H:i') }}</p>
                                @if($version->change_notes)
                                <p class="text-xs text-gray-600 mt-0.5">{{ $version->change_notes }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ number_format($version->file_size / 1024, 1) }} KB</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Upload New Version --}}
            @if(auth()->user()->hasPermission('documents.upload'))
            <div class="card">
                <div class="card-header"><h2 class="text-sm font-semibold">Carica Nuova Versione</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.new-version', $document) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <input type="file" name="file" required class="block w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-100 file:text-brand-800 hover:file:bg-brand-200">
                        </div>
                        <div>
                            <textarea name="change_notes" rows="2" class="form-input text-sm" placeholder="Note sulla modifica..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full text-sm">Carica Versione</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Quick Info --}}
            <div class="card">
                <div class="card-body space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Stato</span>
                        <span class="badge-{{ $document->status_color }}">{{ $document->status_label }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Formato</span>
                        <span class="text-sm font-medium">{{ strtoupper(pathinfo($document->file_name_original, PATHINFO_EXTENSION)) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Aggiornato</span>
                        <span class="text-sm font-medium">{{ $document->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
