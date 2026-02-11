@extends('layouts.guest')

@section('content')
<div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
    {{-- Header --}}
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-white">Accedi al tuo account</h2>
        <p class="text-sm text-brand-200 mt-1">Inserisci le tue credenziali per continuare</p>
    </div>

    {{-- Rate Limiting Message --}}
    @if (session('status'))
        <div class="mb-4 bg-green-500/20 border border-green-400/30 rounded-lg px-4 py-3 text-sm text-green-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- Throttle / Rate Limit Error --}}
    @error('throttle')
        <div class="mb-4 bg-red-500/20 border border-red-400/30 rounded-lg px-4 py-3 text-sm text-red-200 flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-brand-100 mb-1.5">Indirizzo Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="nome@esempio.it"
                    class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-brand-300 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:border-transparent transition @error('email') border-red-400 @enderror"
                >
            </div>
            @error('email')
                <p class="mt-1.5 text-sm text-red-300 flex items-center gap-1">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-brand-100 mb-1.5">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Inserisci la password"
                    class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-brand-300 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:border-transparent transition @error('password') border-red-400 @enderror"
                >
            </div>
            @error('password')
                <p class="mt-1.5 text-sm text-red-300 flex items-center gap-1">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-white/30 bg-white/10 text-gold-500 focus:ring-gold-400 focus:ring-offset-0"
                >
                <span class="text-sm text-brand-200">Ricordami</span>
            </label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="btn-primary w-full flex items-center justify-center gap-2 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200 shadow-lg hover:shadow-xl"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Accedi
        </button>
    </form>
</div>

{{-- Footer --}}
<div class="text-center mt-6">
    <p class="text-sm text-brand-300">&copy; {{ date('Y') }} {{ config('app.name') }}. Tutti i diritti riservati.</p>
</div>
@endsection
