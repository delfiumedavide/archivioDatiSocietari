@extends('layouts.app')
@section('title', 'Registro Attivita')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Registro Attivita</span>
@endsection

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Registro Attivita</h1>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('activity-log.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <select name="user_id" class="form-select">
                        <option value="">Tutti gli utenti</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="action" class="form-select">
                        <option value="">Tutte le azioni</option>
                        <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Creazione</option>
                        <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Modifica</option>
                        <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Eliminazione</option>
                        <option value="uploaded" {{ request('action') === 'uploaded' ? 'selected' : '' }}>Caricamento</option>
                        <option value="downloaded" {{ request('action') === 'downloaded' ? 'selected' : '' }}>Download</option>
                        <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Accesso</option>
                        <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Uscita</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input" placeholder="Da">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input" placeholder="A">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtra</button>
                    <a href="{{ route('activity-log.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Data/Ora</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Utente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Azione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Descrizione</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $log->user?->name ?? 'Sistema' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $log->action_color }}-100 text-{{ $log->action_color }}-800">{{ $log->action_label }}</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-700 max-w-md truncate">{{ $log->description }}</td>
                        <td class="px-6 py-3 text-xs text-gray-500 font-mono">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Nessuna attivita registrata.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
