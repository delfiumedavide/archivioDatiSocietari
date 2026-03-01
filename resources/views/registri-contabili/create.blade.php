@extends('layouts.app')

@section('title', 'Carica Registro Contabile')

@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('registri-contabili.index') }}" class="text-gray-600 hover:text-gray-900">Libri e Registri Contabili</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Carica</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Carica Libro / Registro Contabile</h1>

    <form method="POST" action="{{ route('registri-contabili.store') }}" enctype="multipart/form-data"
          class="space-y-6"
          x-data="{
              tipoSelezionato: '{{ old('tipo', request('tipo')) }}',
              tipiMensili: @json($tipiMensili),
              get isMensile() { return this.tipiMensili.includes(this.tipoSelezionato); }
          }">
        @csrf

        {{-- Dati registro --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">Dati Registro</h2></div>
            <div class="card-body space-y-4">

                {{-- Società --}}
                <div>
                    <label for="company_id" class="form-label">Società <span class="text-red-500">*</span></label>
                    <select id="company_id" name="company_id" class="form-select @error('company_id') border-red-300 @enderror" required>
                        <option value="">— Seleziona società —</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id', request('company_id')) == $company->id)>
                                {{ $company->denominazione }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Anno e Tipo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="anno" class="form-label">Anno <span class="text-red-500">*</span></label>
                        <select id="anno" name="anno" class="form-select @error('anno') border-red-300 @enderror" required>
                            <option value="">— Seleziona anno —</option>
                            @foreach($anni as $anno)
                                <option value="{{ $anno }}" @selected(old('anno', request('anno', now()->year)) == $anno)>{{ $anno }}</option>
                            @endforeach
                        </select>
                        @error('anno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="tipo" class="form-label">Tipo di registro <span class="text-red-500">*</span></label>
                        <select id="tipo" name="tipo" class="form-select @error('tipo') border-red-300 @enderror"
                                x-model="tipoSelezionato" required>
                            <option value="">— Seleziona tipo —</option>
                            @foreach($tipi as $slug => $label)
                                <option value="{{ $slug }}" @selected(old('tipo', request('tipo')) === $slug)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Mese (solo IVA mensili) --}}
                <div x-show="isMensile" x-cloak>
                    <label for="mese" class="form-label">
                        Mese <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-400 ml-1">richiesto per registri IVA mensili</span>
                    </label>
                    <select id="mese" name="mese" class="form-select @error('mese') border-red-300 @enderror"
                            :required="isMensile">
                        <option value="">— Seleziona mese —</option>
                        @foreach($mesi as $num => $label)
                            <option value="{{ $num }}" @selected(old('mese', request('mese')) == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('mese')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- File e dettagli --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold text-gray-900">File e Dettagli</h2></div>
            <div class="card-body space-y-4">

                {{-- Titolo --}}
                <div>
                    <label for="titolo" class="form-label">Titolo <span class="text-red-500">*</span></label>
                    <input type="text" id="titolo" name="titolo" value="{{ old('titolo') }}"
                           class="form-input @error('titolo') border-red-300 @enderror"
                           placeholder="es. Libro Giornale 2025" required maxlength="255">
                    @error('titolo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- File --}}
                <div>
                    <label class="form-label">File <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required
                           class="block w-full text-sm text-gray-500
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700
                                  hover:file:bg-brand-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, immagini, ZIP, p7m — max 50 MB</p>
                    @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Note --}}
                <div>
                    <label for="note" class="form-label">Note</label>
                    <textarea id="note" name="note" rows="2"
                              class="form-input @error('note') border-red-300 @enderror"
                              placeholder="Note aggiuntive facoltative...">{{ old('note') }}</textarea>
                    @error('note')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Carica Registro
            </button>
            <a href="{{ route('registri-contabili.index') }}" class="btn-secondary">Annulla</a>
        </div>

    </form>
</div>
@endsection
