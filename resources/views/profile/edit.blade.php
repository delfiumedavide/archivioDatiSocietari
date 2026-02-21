@extends('layouts.app')

@section('title', 'Il mio profilo')

@section('breadcrumb')
    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Profilo</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Il mio profilo</h1>
        <p class="text-sm text-gray-500 mt-1">Gestisci i tuoi dati personali e la password di accesso.</p>
    </div>

    {{-- Dati profilo --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Informazioni personali</h2>
        </div>
        <div class="px-6 py-5">
            @if(session('success') && !session('password_success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4 mb-5">
                    <div class="w-16 h-16 bg-brand-600 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->roles->first()?->label ?? 'Utente' }}</p>
                        @if($user->last_login_at)
                        <p class="text-xs text-gray-400 mt-0.5">Ultimo accesso: {{ $user->last_login_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="form-input w-full @error('name') border-red-300 @enderror">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="form-input w-full @error('email') border-red-300 @enderror">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salva modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cambio password --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Modifica password</h2>
            <p class="text-xs text-gray-500 mt-0.5">Scegli una password sicura di almeno 8 caratteri.</p>
        </div>
        <div class="px-6 py-5">
            @if(session('password_success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('password_success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password attuale</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="form-input w-full @error('current_password') border-red-300 @enderror">
                    @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuova password</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="form-input w-full @error('password') border-red-300 @enderror">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conferma nuova password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="form-input w-full">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Aggiorna password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Società accessibili (solo se non admin) --}}
    @if(!auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Società accessibili</h2>
            <p class="text-xs text-gray-500 mt-0.5">Le società che puoi visualizzare e gestire.</p>
        </div>
        <div class="px-6 py-4">
            @php $companies = auth()->user()->load('companies')->companies; @endphp
            @if($companies->isEmpty())
            <p class="text-sm text-gray-500 italic">Nessuna società assegnata. Contatta un amministratore.</p>
            @else
            <div class="flex flex-wrap gap-2">
                @foreach($companies as $company)
                <a href="{{ route('companies.show', $company) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-brand-50 text-brand-700 hover:bg-brand-100 transition border border-brand-200">
                    {{ $company->denominazione }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
