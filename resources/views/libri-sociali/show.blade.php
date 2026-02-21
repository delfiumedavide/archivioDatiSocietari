@extends('layouts.app')
@section('title', $riunione->tipo_short . ' — ' . $riunione->data_ora->format('d/m/Y'))
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('libri-sociali.index') }}" class="text-gray-600 hover:text-gray-900">Libri Sociali</a>
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">{{ $riunione->tipo_short }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ showPresenze: false, showDelibereaForm: false }">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Tipo badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        @if($riunione->tipo === 'cda') bg-brand-100 text-brand-800
                        @elseif($riunione->tipo === 'collegio_sindacale') bg-purple-100 text-purple-800
                        @elseif($riunione->tipo === 'assemblea_ordinaria') bg-teal-100 text-teal-800
                        @else bg-orange-100 text-orange-800 @endif">
                        {{ $riunione->tipo_label }}
                    </span>
                    {{-- Status badge --}}
                    @php $sc = $riunione->status_color @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($sc === 'green') bg-green-100 text-green-800
                        @elseif($sc === 'blue') bg-blue-100 text-blue-800
                        @elseif($sc === 'red') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $riunione->status_label }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mt-2">{{ $riunione->company->denominazione }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    <span>{{ $riunione->data_ora->format('d/m/Y') }} — ore {{ $riunione->data_ora->format('H:i') }}</span>
                    @if($riunione->luogo)
                    <span class="mx-2 text-gray-300">·</span>
                    <span>{{ $riunione->luogo }}</span>
                    @endif
                </p>
            </div>

            {{-- Azioni header --}}
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('libri-sociali.edit', $riunione) }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Modifica
                </a>

                {{-- Avanzamento status --}}
                @if($riunione->status === 'programmata')
                <form action="{{ route('libri-sociali.status', $riunione) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="convocata">
                    <button type="submit" class="btn-primary text-sm">Segna come Convocata</button>
                </form>
                @elseif($riunione->status === 'convocata')
                <form action="{{ route('libri-sociali.status', $riunione) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="svolta">
                    <button type="submit" class="btn-primary text-sm">Chiudi Riunione (Svolta)</button>
                </form>
                @endif

                @if($riunione->status !== 'annullata' && $riunione->status !== 'svolta')
                <form action="{{ route('libri-sociali.status', $riunione) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="annullata">
                    <button type="submit" class="text-sm text-red-600 hover:underline px-3 py-2"
                        onclick="return confirm('Annullare questa riunione?')">Annulla riunione</button>
                </form>
                @endif

                <form action="{{ route('libri-sociali.destroy', $riunione) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:underline px-3 py-2"
                        onclick="return confirm('Eliminare definitivamente questa riunione e tutti i suoi dati?')">Elimina</button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonna principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Ordine del Giorno --}}
            @if($riunione->ordine_del_giorno)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-3">Ordine del Giorno</h2>
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $riunione->ordine_del_giorno }}</div>
            </div>
            @endif

            {{-- Delibere --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Delibere</h2>
                    <button @click="showDelibereaForm = !showDelibereaForm"
                        class="text-sm text-brand-600 hover:underline font-medium">
                        <span x-show="!showDelibereaForm">+ Aggiungi</span>
                        <span x-show="showDelibereaForm">Annulla</span>
                    </button>
                </div>

                {{-- Form aggiungi delibera --}}
                <div x-show="showDelibereaForm" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <form action="{{ route('libri-sociali.delibere.store', $riunione) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Oggetto <span class="text-red-500">*</span></label>
                                <input type="text" name="oggetto" placeholder="Oggetto della delibera" class="form-input text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Esito</label>
                                <select name="esito" class="form-select text-sm">
                                    <option value="approvata">Approvata</option>
                                    <option value="respinta">Respinta</option>
                                    <option value="sospesa">Sospesa</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                            <textarea name="note" rows="2" class="form-input text-sm" placeholder="Dettagli aggiuntivi..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary text-sm">Aggiungi Delibera</button>
                    </form>
                </div>

                {{-- Lista delibere --}}
                @if($riunione->delibere->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($riunione->delibere as $delibera)
                    <div class="px-6 py-4" x-data="{ editing: false }">
                        {{-- View mode --}}
                        <div x-show="!editing" class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 text-gray-600 text-xs font-bold flex items-center justify-center mt-0.5">{{ $delibera->numero }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $delibera->oggetto }}</p>
                                    @if($delibera->note)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $delibera->note }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($delibera->esito_color === 'green') bg-green-100 text-green-800
                                    @elseif($delibera->esito_color === 'red') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $delibera->esito_label }}
                                </span>
                                <button @click="editing = true" class="text-xs text-gray-400 hover:text-brand-600">Modifica</button>
                                <form action="{{ route('delibere.destroy', $delibera) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-gray-400 hover:text-red-600"
                                        onclick="return confirm('Eliminare questa delibera?')">Elimina</button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit mode --}}
                        <div x-show="editing" x-transition>
                            <form action="{{ route('delibere.update', $delibera) }}" method="POST" class="space-y-3">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="md:col-span-2">
                                        <input type="text" name="oggetto" value="{{ $delibera->oggetto }}" class="form-input text-sm" required>
                                    </div>
                                    <div>
                                        <select name="esito" class="form-select text-sm">
                                            <option value="approvata" {{ $delibera->esito === 'approvata' ? 'selected' : '' }}>Approvata</option>
                                            <option value="respinta" {{ $delibera->esito === 'respinta' ? 'selected' : '' }}>Respinta</option>
                                            <option value="sospesa" {{ $delibera->esito === 'sospesa' ? 'selected' : '' }}>Sospesa</option>
                                        </select>
                                    </div>
                                </div>
                                <textarea name="note" rows="2" class="form-input text-sm">{{ $delibera->note }}</textarea>
                                <div class="flex gap-2">
                                    <button type="submit" class="btn-primary text-sm">Salva</button>
                                    <button type="button" @click="editing = false" class="btn-secondary text-sm">Annulla</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center text-sm text-gray-400">
                    Nessuna delibera registrata.
                    <button @click="showDelibereaForm = true" class="text-brand-600 hover:underline ml-1">Aggiungi la prima →</button>
                </div>
                @endif
            </div>

            {{-- Presenti --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Presenti</h2>
                    @if($companyMembers->isNotEmpty())
                    <button @click="showPresenze = !showPresenze" class="text-sm text-brand-600 hover:underline font-medium">
                        <span x-show="!showPresenze">Gestisci presenze</span>
                        <span x-show="showPresenze">Chiudi</span>
                    </button>
                    @endif
                </div>

                {{-- Form presenze --}}
                <div x-show="showPresenze" x-transition class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <form action="{{ route('libri-sociali.partecipanti.store', $riunione) }}" method="POST" class="space-y-3">
                        @csrf
                        <p class="text-xs text-gray-500 mb-3">Seleziona i membri della societa e il loro stato di presenza.</p>
                        @foreach($companyMembers as $i => $member)
                        <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                            <input type="hidden" name="partecipanti[{{ $i }}][member_id]" value="{{ $member->id }}">
                            <span class="text-sm font-medium text-gray-800 flex-1">{{ $member->full_name }}</span>
                            <select name="partecipanti[{{ $i }}][presenza]" class="form-select text-sm w-36">
                                <option value="presente" {{ ($partecipantiMap[$member->id]->presenza ?? '') === 'presente' ? 'selected' : '' }}>Presente</option>
                                <option value="assente" {{ ($partecipantiMap[$member->id]->presenza ?? '') === 'assente' ? 'selected' : '' }}>Assente</option>
                                <option value="delegato" {{ ($partecipantiMap[$member->id]->presenza ?? '') === 'delegato' ? 'selected' : '' }}>Delegato</option>
                            </select>
                            <input type="text" name="partecipanti[{{ $i }}][note]"
                                value="{{ $partecipantiMap[$member->id]->note ?? '' }}"
                                placeholder="Note (es. nome delegante)"
                                class="form-input text-sm flex-1">
                        </div>
                        @endforeach
                        <button type="submit" class="btn-primary text-sm">Salva Presenze</button>
                    </form>
                </div>

                {{-- Lista presenti correnti --}}
                @if($riunione->partecipanti->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($riunione->partecipanti as $p)
                    <div class="flex items-center justify-between px-6 py-3">
                        <span class="text-sm text-gray-800">{{ $p->member->full_name }}</span>
                        <div class="flex items-center gap-2">
                            @if($p->note)
                            <span class="text-xs text-gray-400">{{ $p->note }}</span>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($p->presenza_color === 'green') bg-green-100 text-green-800
                                @elseif($p->presenza_color === 'red') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $p->presenza_label }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center text-sm text-gray-400">
                    Nessun partecipante registrato.
                    @if($companyMembers->isNotEmpty())
                    <button @click="showPresenze = true" class="text-brand-600 hover:underline ml-1">Gestisci presenze →</button>
                    @endif
                </div>
                @endif
            </div>

            {{-- Note interne --}}
            @if($riunione->note)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-2">Note interne</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $riunione->note }}</p>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Card Convocazione --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Convocazione
                </h3>
                @if($riunione->has_convocazione)
                <a href="{{ route('libri-sociali.download-convocazione', $riunione) }}"
                    class="flex items-center gap-2 text-sm text-brand-600 hover:underline mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Scarica PDF convocazione
                </a>
                <p class="text-xs text-gray-400 mb-3">Sostituisci con un file aggiornato:</p>
                @else
                <p class="text-xs text-gray-500 mb-3">Nessuna convocazione caricata.</p>
                @endif
                <form action="{{ route('libri-sociali.upload-convocazione', $riunione) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="convocazione" accept=".pdf" class="text-xs text-gray-600 mb-2 block w-full">
                    <button type="submit" class="btn-secondary text-xs w-full mt-2">
                        {{ $riunione->has_convocazione ? 'Sostituisci' : 'Carica' }} Convocazione
                    </button>
                </form>
            </div>

            {{-- Card Verbale --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Verbale
                </h3>
                @if($riunione->has_verbale)
                <a href="{{ route('libri-sociali.download-verbale', $riunione) }}"
                    class="flex items-center gap-2 text-sm text-brand-600 hover:underline mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Scarica PDF verbale
                </a>
                <p class="text-xs text-gray-400 mb-3">Sostituisci con un file aggiornato:</p>
                @else
                <p class="text-xs text-gray-500 mb-3">Nessun verbale caricato.</p>
                @endif
                <form action="{{ route('libri-sociali.upload-verbale', $riunione) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="verbale" accept=".pdf" class="text-xs text-gray-600 mb-2 block w-full">
                    <button type="submit" class="btn-secondary text-xs w-full mt-2">
                        {{ $riunione->has_verbale ? 'Sostituisci' : 'Carica' }} Verbale
                    </button>
                </form>
            </div>

            {{-- Card Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Informazioni</h3>
                <dl class="space-y-2 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <dt class="font-medium">Societa</dt>
                        <dd><a href="{{ route('companies.show', $riunione->company) }}" class="text-brand-600 hover:underline">{{ $riunione->company->denominazione }}</a></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium">Creata da</dt>
                        <dd>{{ $riunione->creator?->name ?? 'Sistema' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium">Creata il</dt>
                        <dd>{{ $riunione->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium">Delibere</dt>
                        <dd>{{ $riunione->delibere->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium">Presenti registrati</dt>
                        <dd>{{ $riunione->partecipanti->count() }}</dd>
                    </div>
                </dl>
            </div>

        </div>
    </div>
</div>
@endsection
