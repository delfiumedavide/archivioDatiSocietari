@extends('layouts.app')

@section('title', 'Completezza Registri ' . $anno)

@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('registri-contabili.index') }}" class="text-gray-600 hover:text-gray-900">Libri e Registri Contabili</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Completezza {{ $anno }}</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Completezza Registri</h1>
            <p class="mt-1 text-sm text-gray-500">Verifica quali libri e registri sono stati caricati per ogni società</p>
        </div>
        <a href="{{ route('registri-contabili.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Torna all'archivio
        </a>
    </div>

    {{-- Filtro anno --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('registri-contabili.completezza') }}" class="flex items-end gap-4">
                <div>
                    <label class="form-label">Anno di riferimento</label>
                    <select name="anno" class="form-select">
                        @foreach($anni as $a)
                            <option value="{{ $a }}" @selected($a == $anno)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Aggiorna</button>
            </form>
        </div>
    </div>

    @if($companies->isEmpty())
        <div class="card text-center py-12">
            <p class="text-gray-500">Nessuna società accessibile.</p>
        </div>
    @else

    {{-- Legenda --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 text-sm text-gray-600">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Registro
            </span>
            Presente
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-400 text-xs font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registro
            </span>
            Mancante <span class="text-gray-400 text-xs ml-1">(clicca per caricare)</span>
        </div>
    </div>

    {{-- =====================================================================
         SEZIONE 1: Registri Annuali
    ===================================================================== --}}
    @php
        $totaleAttesiAnnuali   = $companies->count() * count($tipiAnnuali);
        $totalePresentiAnnuali = $presentiAnnuali->sum(fn($c) => $c->count());
        $percAnnuale  = $totaleAttesiAnnuali > 0 ? round($totalePresentiAnnuali / $totaleAttesiAnnuali * 100) : 0;
        $colorAnnuale = $percAnnuale >= 80 ? 'text-green-600' : ($percAnnuale >= 50 ? 'text-amber-500' : 'text-red-500');
        $barAnnuale   = $percAnnuale >= 80 ? 'bg-green-500'   : ($percAnnuale >= 50 ? 'bg-amber-400'   : 'bg-red-400');
    @endphp

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2.5">
                <span class="inline-block w-1 h-5 bg-brand-600 rounded-full"></span>
                Registri Annuali &mdash; {{ $anno }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">{{ $totalePresentiAnnuali }}/{{ $totaleAttesiAnnuali }} caricati</span>
                <span class="text-sm font-bold {{ $colorAnnuale }}">{{ $percAnnuale }}%</span>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="divide-y divide-gray-200 text-sm" style="min-width: 100%;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-th sticky left-0 bg-gray-50 z-10 min-w-52">Società</th>
                            @foreach($tipiAnnuali as $slug => $label)
                            <th class="table-th text-center align-top" style="min-width: 130px;">
                                <span class="text-xs font-medium text-gray-600 leading-snug">{{ $label }}</span>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($companies as $company)
                        @php $companyPresenti = $presentiAnnuali->get($company->id, collect()); @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="table-td sticky left-0 bg-white font-medium text-gray-900 border-r border-gray-100 z-10">
                                <a href="{{ route('companies.show', $company) }}" class="hover:text-brand-600">
                                    {{ $company->denominazione }}
                                </a>
                            </td>
                            @foreach($tipiAnnuali as $slug => $label)
                            @php $presente = $companyPresenti->has($slug); @endphp
                            <td class="table-td text-center">
                                @if($presente)
                                    <a href="{{ route('registri-contabili.show', $companyPresenti->get($slug)->id) }}"
                                       title="{{ $label }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 hover:bg-green-200 transition">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </a>
                                @elseif(auth()->user()->hasPermission('registri_contabili.upload'))
                                    <a href="{{ route('registri-contabili.create', ['company_id' => $company->id, 'anno' => $anno, 'tipo' => $slug]) }}"
                                       title="Carica: {{ $label }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-brand-100 hover:text-brand-600 text-gray-300 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100">
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-3 px-1">
            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $barAnnuale }}" style="width: {{ $percAnnuale }}%"></div>
            </div>
            <span class="text-xs text-gray-400">{{ $totalePresentiAnnuali }}/{{ $totaleAttesiAnnuali }}</span>
        </div>
    </div>

    {{-- =====================================================================
         SEZIONE 2: Registri IVA Mensili — pill per tipo
    ===================================================================== --}}
    @php
        $totMesiAttesi   = 0;
        $totMesiPresenti = 0;
        foreach ($companies as $company) {
            $presentiC = $presentiMensili->get($company->id, collect());
            $required  = count($tipiMensiliStd) + ($company->gestisce_iva_margine ? count($tipiMensiliMargine) : 0);
            foreach ($mesiDaVerificare as $mese) {
                $totMesiAttesi   += $required;
                $totMesiPresenti += $presentiC->get((string)$mese, collect())->count();
            }
        }
        $percMensile  = $totMesiAttesi > 0 ? round($totMesiPresenti / $totMesiAttesi * 100) : 0;
        $colorMensile = $percMensile >= 80 ? 'text-green-600' : ($percMensile >= 50 ? 'text-amber-500' : 'text-red-500');
        $barMensile   = $percMensile >= 80 ? 'bg-green-500'   : ($percMensile >= 50 ? 'bg-amber-400'   : 'bg-red-400');
        $canUpload    = auth()->user()->hasPermission('registri_contabili.upload');
    @endphp

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2.5">
                <span class="inline-block w-1 h-5 bg-brand-600 rounded-full"></span>
                Registri IVA Mensili &mdash; {{ $anno }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">{{ $totMesiPresenti }}/{{ $totMesiAttesi }} caricati</span>
                <span class="text-sm font-bold {{ $colorMensile }}">{{ $percMensile }}%</span>
            </div>
        </div>

        {{-- Un card per ogni mese --}}
        @foreach($mesiDaVerificare as $mese)
        <div class="card p-0 overflow-hidden">

            {{-- Header mese --}}
            @php
                $meseLabel = $mesiLabels[$mese];
                $totMesePresenti = 0;
                $totMeseAttesi   = 0;
                foreach ($companies as $c) {
                    $req = count($tipiMensiliStd) + ($c->gestisce_iva_margine ? count($tipiMensiliMargine) : 0);
                    $totMeseAttesi   += $req;
                    $totMesePresenti += $presentiMensili->get($c->id, collect())->get((string)$mese, collect())->count();
                }
                $percMese   = $totMeseAttesi > 0 ? round($totMesePresenti / $totMeseAttesi * 100) : 0;
                $badgeMese  = $percMese >= 100 ? 'bg-green-100 text-green-700'
                            : ($percMese > 0   ? 'bg-amber-100 text-amber-700'
                                               : 'bg-gray-100 text-gray-500');
            @endphp
            <div class="card-header flex items-center justify-between py-3">
                <h3 class="text-sm font-semibold text-gray-800">{{ $meseLabel }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeMese }}">
                    {{ $totMesePresenti }}/{{ $totMeseAttesi }}
                </span>
            </div>

            {{-- Righe società --}}
            <div class="divide-y divide-gray-100">
                @foreach($companies as $company)
                @php
                    $presentiM = $presentiMensili->get($company->id, collect())->get((string)$mese, collect());
                @endphp
                <div class="px-5 py-3 flex flex-wrap items-center gap-x-4 gap-y-2">

                    {{-- Nome società --}}
                    <div class="min-w-40 flex items-center gap-1.5 flex-shrink-0">
                        <a href="{{ route('companies.show', $company) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600">
                            {{ $company->denominazione }}
                        </a>
                        @if($company->gestisce_iva_margine)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-600 font-medium">M</span>
                        @endif
                    </div>

                    {{-- Divider --}}
                    <div class="hidden sm:block w-px h-5 bg-gray-200 flex-shrink-0"></div>

                    {{-- Pill per ogni tipo standard --}}
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($tipiMensiliStd as $slug => $label)
                        @php $reg = $presentiM->get($slug); @endphp
                        @if($reg)
                            <a href="{{ route('registri-contabili.show', $reg->id) }}"
                               title="{{ $label }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $tipiMensiliShort[$slug] }}
                            </a>
                        @elseif($canUpload)
                            <a href="{{ route('registri-contabili.create', ['company_id' => $company->id, 'anno' => $anno, 'tipo' => $slug, 'mese' => $mese]) }}"
                               title="Carica: {{ $label }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 hover:bg-brand-50 hover:text-brand-600 border border-dashed border-gray-300 hover:border-brand-400 transition">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ $tipiMensiliShort[$slug] }}
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-300 border border-gray-200">
                                {{ $tipiMensiliShort[$slug] }}
                            </span>
                        @endif
                        @endforeach

                        {{-- Pill per tipi margine (solo se la società usa IVA Margine) --}}
                        @if($company->gestisce_iva_margine)
                        @foreach($tipiMensiliMargine as $slug => $label)
                        @php $reg = $presentiM->get($slug); @endphp
                        @if($reg)
                            <a href="{{ route('registri-contabili.show', $reg->id) }}"
                               title="{{ $label }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $tipiMensiliShort[$slug] }}
                            </a>
                        @elseif($canUpload)
                            <a href="{{ route('registri-contabili.create', ['company_id' => $company->id, 'anno' => $anno, 'tipo' => $slug, 'mese' => $mese]) }}"
                               title="Carica: {{ $label }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-300 hover:bg-purple-100 hover:text-purple-600 border border-dashed border-purple-200 hover:border-purple-400 transition">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ $tipiMensiliShort[$slug] }}
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-200 border border-purple-100">
                                {{ $tipiMensiliShort[$slug] }}
                            </span>
                        @endif
                        @endforeach
                        @endif
                    </div>

                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex items-center gap-3 px-1">
            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $barMensile }}" style="width: {{ $percMensile }}%"></div>
            </div>
            <span class="text-xs text-gray-400">{{ $totMesiPresenti }}/{{ $totMesiAttesi }}</span>
        </div>
    </div>

    @endif

</div>
@endsection
