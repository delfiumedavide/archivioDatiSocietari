@extends('layouts.app')
@section('title', 'Gestione Utenti')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Utenti</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Gestione Utenti</h1>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nuovo Utente
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Utente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ruolo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Societa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ultimo Accesso</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' : ($role->name === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">{{ $role->label }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4">
                            @if($user->isAdmin())
                                <span class="text-xs text-gray-400 italic">Tutte</span>
                            @elseif($user->companies->isEmpty())
                                <span class="text-xs text-gray-400 italic">Nessuna</span>
                            @else
                                @foreach($user->companies->take(2) as $c)
                                    <span class="inline-block text-xs bg-brand-50 text-brand-700 border border-brand-200 rounded px-1.5 py-0.5 mr-1 mb-1">{{ $c->denominazione }}</span>
                                @endforeach
                                @if($user->companies->count() > 2)
                                    <span class="text-xs text-gray-500">+{{ $user->companies->count() - 2 }} altre</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $user->is_active ? 'badge-green' : 'badge-red' }}">{{ $user->is_active ? 'Attivo' : 'Disattivato' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Mai' }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('users.edit', $user) }}" class="text-brand-600 hover:underline text-sm">Modifica</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Eliminare questo utente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Elimina</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
