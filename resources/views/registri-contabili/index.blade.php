@extends('layouts.app')

@section('title', 'Libri e Registri Contabili')

@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Libri e Registri Contabili</span>
@endsection

@section('content')
<div class="space-y-6"
     x-data="{
         pageIds: @json($registri->pluck('id')),
         selected: [],
         toggleAll(checked) { this.selected = checked ? [...this.pageIds] : []; },
         toggle(id) {
             if (this.selected.includes(id)) {
                 this.selected = this.selected.filter(i => i !== id);
             } else {
                 this.selected.push(id);
             }
         },
         isSelected(id) { return this.selected.includes(id); }
     }">

    {{-- ZIP Export Toolbar (appare quando ci sono selezioni) --}}
    @if(auth()->user()->hasPermission('registri_contabili.download'))
    <div x-show="selected.length > 0" x-transition
         class="bg-brand-600 text-white rounded-xl px-5 py-3 flex items-center justify-between shadow-md">
        <span class="text-sm font-medium">
            <span x-text="selected.length"></span> registro/i selezionato/i
        </span>
        <form method="POST" action="{{ route('registri-contabili.export-zip') }}">
            @csrf
            <input type="hidden" name="ids" :value="selected.join(',')">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-white text-brand-700 font-medium px-4 py-1.5 rounded-lg text-sm hover:bg-brand-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Esporta ZIP
            </button>
        </form>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Libri e Registri Contabili</h1>
            <p class="mt-1 text-sm text-gray-500">Archivio annuale dei libri e registri contabili obbligatori</p>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasSection('registri_contabili'))
            <a href="{{ route('registri-contabili.completezza') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Completezza
            </a>
            @endif
            @if(auth()->user()->hasPermission('registri_contabili.upload'))
            <a href="{{ route('registri-contabili.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Carica Registro
            </a>
            @endif
        </div>
    </div>

    {{-- Filtri --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('registri-contabili.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="form-label">Società</label>
                    <select name="company_id" class="form-select">
                        <option value="">Tutte le società</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(request('company_id') == $company->id)>
                                {{ $company->denominazione }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Anno</label>
                    <select name="anno" class="form-select">
                        <option value="" @selected($annoFilter === '')>Tutti gli anni</option>
                        @foreach($anni as $anno)
                            <option value="{{ $anno }}" @selected((string)$anno === $annoFilter)>{{ $anno }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipo registro</label>
                    <select name="tipo" class="form-select">
                        <option value="">Tutti i tipi</option>
                        @foreach($tipi as $slug => $label)
                            <option value="{{ $slug }}" @selected(request('tipo') === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Mese</label>
                    <select name="mese" class="form-select">
                        <option value="">Tutti i mesi</option>
                        @foreach($mesi as $num => $label)
                            <option value="{{ $num }}" @selected(request('mese') == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtra</button>
                    <a href="{{ route('registri-contabili.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="card p-0 overflow-hidden">
        @if($registri->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h6m-6 4h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                </svg>
                <p class="text-gray-500">Nessun registro contabile trovato.</p>
                @if(auth()->user()->hasPermission('registri_contabili.upload'))
                    <a href="{{ route('registri-contabili.create') }}" class="mt-3 inline-block text-brand-600 hover:underline text-sm">Carica il primo registro</a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(auth()->user()->hasPermission('registri_contabili.download'))
                            <th class="table-th w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                       @change="toggleAll($event.target.checked)">
                            </th>
                            @endif
                            <th class="table-th">Titolo</th>
                            <th class="table-th">Tipo</th>
                            <th class="table-th">Anno / Mese</th>
                            <th class="table-th">Società</th>
                            <th class="table-th text-center">Versione</th>
                            <th class="table-th">Dimensione</th>
                            <th class="table-th">Caricato</th>
                            <th class="table-th text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($registri as $registro)
                        <tr class="hover:bg-gray-50 transition" :class="isSelected({{ $registro->id }}) ? 'bg-brand-50' : ''">
                            @if(auth()->user()->hasPermission('registri_contabili.download'))
                            <td class="table-td w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                       :checked="isSelected({{ $registro->id }})"
                                       @change="toggle({{ $registro->id }})">
                            </td>
                            @endif
                            <td class="table-td font-medium text-gray-900">
                                <a href="{{ route('registri-contabili.show', $registro) }}" class="hover:text-brand-600">
                                    {{ $registro->titolo }}
                                </a>
                            </td>
                            <td class="table-td">
                                <span class="badge badge-gray">{{ $registro->tipo_label }}</span>
                            </td>
                            <td class="table-td font-medium">
                                {{ $registro->anno }}
                                @if($registro->mese)
                                    <span class="block text-xs text-gray-500 font-normal">{{ $registro->mese_label }}</span>
                                @endif
                            </td>
                            <td class="table-td text-gray-600">{{ $registro->company->denominazione ?? '-' }}</td>
                            <td class="table-td text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-brand-100 text-brand-700 text-xs font-bold">
                                    v{{ $registro->current_version }}
                                </span>
                            </td>
                            <td class="table-td text-gray-500 text-sm">{{ $registro->file_size_formatted }}</td>
                            <td class="table-td text-gray-500 text-sm">
                                {{ $registro->created_at->format('d/m/Y') }}
                            </td>
                            <td class="table-td text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('registri-contabili.show', $registro) }}" class="text-gray-400 hover:text-brand-600 transition" title="Dettaglio">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if(auth()->user()->hasPermission('registri_contabili.download'))
                                    <a href="{{ route('registri-contabili.download', $registro) }}" class="text-gray-400 hover:text-green-600 transition" title="Scarica">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('registri_contabili.delete'))
                                    <form method="POST" action="{{ route('registri-contabili.destroy', $registro) }}"
                                          onsubmit="return confirm('Eliminare questo registro? L\'operazione non elimina fisicamente il file.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Elimina">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($registri->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $registri->links() }}
            </div>
            @endif
        @endif
    </div>

</div>
@endsection
