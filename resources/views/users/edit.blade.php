@extends('layouts.app')
@section('title', 'Modifica Utente')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('users.index') }}" class="text-brand-600 hover:underline">Utenti</a>
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Modifica Utente: {{ $user->name }}</h1>

    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold">Dati Utente</h2></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="form-label">Nuova Password</label>
                        <input type="password" name="password" id="password" class="form-input" minlength="8">
                        <p class="text-xs text-gray-500 mt-1">Lascia vuoto per mantenere la password attuale</p>
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Conferma Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="role_id" class="form-label">Ruolo *</label>
                        <select name="role_id" id="role_id" class="form-select" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Utente attivo</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold">Permessi Sezioni</h2></div>
            <div class="card-body">
                @php $userPermIds = old('permissions', $user->permissions->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($permissions as $section => $perms)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 capitalize">{{ str_replace('_', ' ', $section) }}</h3>
                        <div class="space-y-2">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" {{ in_array($perm->id, $userPermIds) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $perm->label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Accesso Società --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Accesso Società</h2>
                <p class="text-sm text-gray-500 mt-0.5">Le società selezionate saranno l'unico dato visibile all'utente. Gli amministratori vedono tutto automaticamente.</p>
            </div>
            <div class="card-body">
                @if($companies->isEmpty())
                    <p class="text-sm text-gray-400 italic">Nessuna società attiva presente.</p>
                @else
                    @php $selectedIds = old('company_ids', $assignedIds); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
                        @foreach($companies as $c)
                        <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="company_ids[]" value="{{ $c->id }}"
                                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                {{ in_array($c->id, $selectedIds) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $c->denominazione }}</span>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('users.index') }}" class="btn-secondary">Annulla</a>
            <button type="submit" class="btn-primary">Salva Modifiche</button>
        </div>
    </form>
</div>
@endsection
