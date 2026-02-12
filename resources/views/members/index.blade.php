@extends('layouts.app')

@section('title', 'Membri')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700 font-medium">Membri</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Membri</h1>
            <p class="text-sm text-gray-500 mt-1">Anagrafiche persone con documenti e scadenze.</p>
        </div>
        <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuovo Membro
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca per nome o codice fiscale..." class="form-input w-full sm:max-w-md">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">Filtra</button>
            </form>
        </div>

        @if($members->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Membro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cariche</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Doc. Identita</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Doc. CF</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($members as $member)
                            @php
                                $identityDocument = $member->identity_document;
                                $taxCodeDocument = $member->tax_code_document;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                                    <div class="text-xs text-gray-500">CF: {{ $member->codice_fiscale }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $member->officers_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($identityDocument)
                                        <div class="text-xs text-gray-700">{{ $identityDocument->expiration_date?->format('d/m/Y') ?? 'Nessuna scadenza' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-{{ $identityDocument->status_color }}-100 text-{{ $identityDocument->status_color }}-700">{{ $identityDocument->status_label }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Non caricato</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($taxCodeDocument)
                                        <div class="text-xs text-gray-700">{{ $taxCodeDocument->expiration_date?->format('d/m/Y') ?? 'Nessuna scadenza' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-{{ $taxCodeDocument->status_color }}-100 text-{{ $taxCodeDocument->status_color }}-700">{{ $taxCodeDocument->status_label }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Non caricato</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('members.edit', $member) }}" class="text-brand-600 hover:text-brand-700 text-sm">Modifica</a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Eliminare questo membro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Elimina</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $members->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-500">Nessun membro registrato.</p>
            </div>
        @endif
    </div>
</div>
@endsection
