@extends('layouts.app')

@section('title', $registro->titolo)

@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('registri-contabili.index') }}" class="text-gray-600 hover:text-gray-900">Libri e Registri Contabili</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">{{ Str::limit($registro->titolo, 40) }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $registro->titolo }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    {{ $registro->tipo_label }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-700">
                    {{ $registro->anno }}{{ $registro->mese ? ' — ' . $registro->mese_label : '' }}
                </span>
                <span class="text-sm text-gray-500">v{{ $registro->current_version }}</span>
            </div>
            <p class="mt-1 text-sm text-gray-500">{{ $registro->company->denominazione }}</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            @if(auth()->user()->hasPermission('registri_contabili.download'))
            <a href="{{ route('registri-contabili.download', $registro) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Scarica
            </a>
            @endif
            @if(auth()->user()->hasPermission('registri_contabili.delete'))
            <form method="POST" action="{{ route('registri-contabili.destroy', $registro) }}"
                  onsubmit="return confirm('Eliminare questo registro?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Elimina</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonna principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informazioni --}}
            <div class="card">
                <div class="card-header"><h2 class="text-lg font-semibold">Dettagli Registro</h2></div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Società</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                <a href="{{ route('companies.show', $registro->company) }}" class="text-brand-600 hover:underline">
                                    {{ $registro->company->denominazione }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Anno</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->anno }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Tipo registro</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->tipo_label }}</dd>
                        </div>
                        @if($registro->mese)
                        <div>
                            <dt class="text-sm text-gray-500">Mese</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->mese_label }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm text-gray-500">File originale</dt>
                            <dd class="text-sm font-medium text-gray-900 break-all">{{ $registro->file_name_original }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dimensione</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->file_size_formatted }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Caricato da</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->uploader->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Data caricamento</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $registro->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                    @if($registro->note)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <dt class="text-sm text-gray-500 mb-1">Note</dt>
                        <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ $registro->note }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Storico versioni --}}
            @if($registro->versions->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold">Storico Versioni</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Versione corrente: v{{ $registro->current_version }}</p>
                </div>
                <div class="card-body p-0">
                    <div class="divide-y divide-gray-100">
                        @foreach($registro->versions as $version)
                        <div class="px-6 py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900">Versione {{ $version->version }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $version->uploader->name ?? '-' }} &mdash; {{ $version->created_at->format('d/m/Y H:i') }}
                                </p>
                                @if($version->change_notes)
                                <p class="text-xs text-gray-600 mt-0.5 italic">{{ $version->change_notes }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-xs text-gray-400">{{ $version->file_size_formatted }}</span>
                                @if(auth()->user()->hasPermission('registri_contabili.download'))
                                <a href="{{ route('registri-contabili.download-version', [$registro, $version]) }}"
                                   title="Scarica v{{ $version->version }}"
                                   class="text-gray-400 hover:text-green-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Carica nuova versione --}}
            @if(auth()->user()->hasPermission('registri_contabili.upload'))
            <div class="card">
                <div class="card-header"><h2 class="text-sm font-semibold">Carica Nuova Versione</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('registri-contabili.new-version', $registro) }}"
                          enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <input type="file" name="file" required
                                   class="block w-full text-sm text-gray-500
                                          file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                          file:text-sm file:font-medium file:bg-brand-100 file:text-brand-800
                                          hover:file:bg-brand-200">
                            @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <textarea name="change_notes" rows="2" class="form-input text-sm"
                                      placeholder="Note sulla modifica...">{{ old('change_notes') }}</textarea>
                            @error('change_notes')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-primary w-full text-sm">
                            Carica v{{ $registro->current_version + 1 }}
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Info rapida --}}
            <div class="card">
                <div class="card-body space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Versioni totali</span>
                        <span class="text-sm font-medium text-gray-900">{{ $registro->current_version }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Formato</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ strtoupper(pathinfo($registro->file_name_original, PATHINFO_EXTENSION)) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Aggiornato</span>
                        <span class="text-sm font-medium text-gray-900">{{ $registro->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
