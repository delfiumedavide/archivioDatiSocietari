<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appSettings->login_title ?? $appSettings->app_name ?? config('app.name') }} - Login</title>
    @if($appSettings->favicon_url ?? false)
        <link rel="icon" type="image/x-icon" href="{{ $appSettings->favicon_url }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ $appSettings->logo_url ?? asset('images/logo-icon.svg') }}" alt="Logo" class="w-20 h-20 mx-auto mb-4 drop-shadow-lg object-contain">
            <h1 class="text-2xl font-bold text-white">{{ $appSettings->login_title ?? $appSettings->app_name ?? 'Archivio Dati Societari' }}</h1>
            <p class="text-gold-400 font-semibold text-lg mt-1">{{ $appSettings->app_subtitle ?? 'Gruppo di Martino' }}</p>
        </div>

        {{-- Content --}}
        {{ $slot ?? '' }}
        @yield('content')
    </div>
</body>
</html>
