@extends('layouts.app')
@section('title', 'Versioni - ' . $document->title)
@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('documents.index') }}" class="text-brand-600 hover:underline">Documenti</a>
<span class="text-gray-400">/</span>
<a href="{{ route('documents.show', $document) }}" class="text-brand-600 hover:underline">{{ Str::limit($document->title, 20) }}</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Versioni</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Storico Versioni</h1>
            <p class="text-gray-500 mt-1">{{ $document->title }} - Versione attuale: v{{ $document->current_version }}</p>
        </div>
        <a href="{{ route('documents.show', $document) }}" class="btn-secondary">Torna al Documento</a>
    </div>

    {{-- Current Version --}}
    <div class="card border-brand-200">
        <div class="card-body flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center">
                    <span class="text-brand-700 font-bold">v{{ $document->current_version }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Versione Corrente</p>
                    <p class="text-xs text-gray-500">{{ $document->uploader->name }} - {{ $document->updated_at->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-gray-500">{{ $document->file_size_formatted }} - {{ $document->file_name_original }}</p>
                </div>
            </div>
            <a href="{{ route('documents.download', $document) }}" class="btn-primary text-sm">Scarica</a>
        </div>
    </div>

    {{-- Previous Versions --}}
    @if($document->versions->isNotEmpty())
    <div class="space-y-3">
        <h2 class="text-lg font-semibold text-gray-700">Versioni Precedenti</h2>
        @foreach($document->versions as $version)
        <div class="card">
            <div class="card-body flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <span class="text-gray-500 font-bold">v{{ $version->version }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Versione {{ $version->version }}</p>
                        <p class="text-xs text-gray-500">{{ $version->uploader->name }} - {{ $version->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($version->file_size / 1048576, 2) }} MB</p>
                        @if($version->change_notes)
                        <p class="text-xs text-gray-600 mt-1 italic">"{{ $version->change_notes }}"</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-8 text-gray-500">
            <p>Questo documento non ha versioni precedenti.</p>
        </div>
    </div>
    @endif
</div>
@endsection
