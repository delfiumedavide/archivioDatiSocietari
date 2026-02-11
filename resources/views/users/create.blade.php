@extends('layouts.app')
@section('title', 'Nuovo Utente')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<a href="{{ route('users.index') }}" class="text-brand-600 hover:underline">Utenti</a>
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Nuovo</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuovo Utente</h1>

    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold">Dati Utente</h2></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input" required>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" name="password" id="password" class="form-input" required minlength="8">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Conferma Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="role_id" class="form-label">Ruolo *</label>
                        <select name="role_id" id="role_id" class="form-select" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->label }} - {{ $role->description }}</option>
                            @endforeach
                        </select>
                        @error('role_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Utente attivo</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold">Permessi Sezioni</h2></div>
            <div class="card-body">
                <p class="text-sm text-gray-500 mb-4">Seleziona i permessi per l'utente. Gli amministratori hanno accesso completo automaticamente.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($permissions as $section => $perms)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 capitalize">{{ str_replace('_', ' ', $section) }}</h3>
                        <div class="space-y-2">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $perm->label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('users.index') }}" class="btn-secondary">Annulla</a>
            <button type="submit" class="btn-primary">Crea Utente</button>
        </div>
    </form>
</div>
@endsection
