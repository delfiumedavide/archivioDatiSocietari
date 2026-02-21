@extends('layouts.app')
@section('title', 'Nuova Riunione')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('libri-sociali.index') }}" class="text-gray-600 hover:text-gray-900">Libri Sociali</a>
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Nuova Riunione</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuova Riunione</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('libri-sociali.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Societa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Societa <span class="text-red-500">*</span></label>
                <select name="company_id" class="form-select @error('company_id') border-red-300 @enderror" required>
                    <option value="">— Seleziona societa —</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->denominazione }}</option>
                    @endforeach
                </select>
                @error('company_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Riunione <span class="text-red-500">*</span></label>
                <select name="tipo" class="form-select @error('tipo') border-red-300 @enderror" required>
                    <option value="">— Seleziona tipo —</option>
                    <option value="cda" {{ old('tipo') === 'cda' ? 'selected' : '' }}>Consiglio di Amministrazione (CDA)</option>
                    <option value="collegio_sindacale" {{ old('tipo') === 'collegio_sindacale' ? 'selected' : '' }}>Collegio Sindacale</option>
                    <option value="assemblea_ordinaria" {{ old('tipo') === 'assemblea_ordinaria' ? 'selected' : '' }}>Assemblea Ordinaria</option>
                    <option value="assemblea_straordinaria" {{ old('tipo') === 'assemblea_straordinaria' ? 'selected' : '' }}>Assemblea Straordinaria</option>
                </select>
                @error('tipo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Data e Ora --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data e Ora <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="data_ora"
                    value="{{ old('data_ora') }}"
                    class="form-input @error('data_ora') border-red-300 @enderror" required>
                @error('data_ora')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Luogo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Luogo</label>
                <input type="text" name="luogo"
                    value="{{ old('luogo') }}"
                    placeholder="Sede legale, indirizzo o link videoconferenza"
                    class="form-input @error('luogo') border-red-300 @enderror">
                @error('luogo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Ordine del Giorno --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordine del Giorno</label>
                <textarea name="ordine_del_giorno" rows="5"
                    placeholder="1. Approvazione bilancio&#10;2. Nomina organi&#10;3. Varie ed eventuali"
                    class="form-input @error('ordine_del_giorno') border-red-300 @enderror">{{ old('ordine_del_giorno') }}</textarea>
                @error('ordine_del_giorno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Note --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note interne</label>
                <textarea name="note" rows="3"
                    class="form-input @error('note') border-red-300 @enderror">{{ old('note') }}</textarea>
                @error('note')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">Crea Riunione</button>
                <a href="{{ route('libri-sociali.index') }}" class="btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
