@extends('layouts.app')
@section('title', 'Scadenze Documenti')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('documents.index') }}" class="text-brand-600 hover:underline">Documenti</a>
<svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Scadenze</span>
@endsection

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Scadenze Documenti</h1>

    {{-- Expired --}}
    @if($expired->isNotEmpty())
    <div class="card border-red-200">
        <div class="card-header bg-red-50 border-red-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <h2 class="text-lg font-semibold text-red-800">Documenti Scaduti ({{ $expired->count() }})</h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Associato a</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scaduto il</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expired as $doc)
                    <tr class="bg-red-50/50">
                        <td class="px-6 py-3"><a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium text-brand-900 hover:underline">{{ $doc->title }}</a></td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $doc->owner_name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $doc->category?->label ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-red-700 font-medium">{{ $doc->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="badge-red">{{ abs($doc->days_until_expiration) }} gg fa</span></td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('documents.show', $doc) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Expiring --}}
    @if($expiring->isNotEmpty())
    <div class="card border-yellow-200">
        <div class="card-header bg-yellow-50 border-yellow-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <h2 class="text-lg font-semibold text-yellow-800">In Scadenza ({{ $expiring->count() }})</h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Associato a</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Scade il</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giorni</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expiring as $doc)
                    <tr class="bg-yellow-50/50">
                        <td class="px-6 py-3"><a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium text-brand-900 hover:underline">{{ $doc->title }}</a></td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $doc->owner_name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $doc->category?->label ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-yellow-700 font-medium">{{ $doc->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="badge-yellow">{{ $doc->days_until_expiration }} gg</span></td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('documents.show', $doc) }}" class="text-brand-600 hover:underline text-sm">Gestisci</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($expired->isEmpty() && $expiring->isEmpty())
    <div class="card">
        <div class="card-body text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-lg font-semibold text-gray-900">Tutto in ordine!</h3>
            <p class="text-gray-500 mt-1">Non ci sono documenti in scadenza o scaduti.</p>
        </div>
    </div>
    @endif
</div>
@endsection
