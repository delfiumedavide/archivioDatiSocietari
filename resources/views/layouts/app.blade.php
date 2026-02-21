<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ $appSettings->favicon_url ?? asset('images/logo-icon.svg') }}" type="image/svg+xml">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar Overlay (mobile) --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-950 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto flex flex-col">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-brand-800">
                <img src="{{ $appSettings->logo_url ?? asset('images/logo-icon.svg') }}" alt="Logo" class="w-10 h-10 flex-shrink-0">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-white leading-tight truncate">{{ $appSettings->app_name }}</h2>
                    <p class="text-xs text-gold-400 font-medium truncate">{{ $appSettings->app_subtitle }}</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                @if(auth()->user()->hasSection('companies') || auth()->user()->hasSection('membri') || auth()->user()->hasSection('stati_famiglia') || auth()->user()->isAdmin())
                <div class="pt-3">
                    <p class="px-4 text-xs font-semibold text-brand-400 uppercase tracking-wider">Gestione</p>
                </div>
                @endif

                @if(auth()->user()->hasSection('companies'))
                <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Societa</span>
                </a>
                @endif

                @if(auth()->user()->hasSection('membri'))
                <a href="{{ route('members.index') }}" class="{{ request()->routeIs('members.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Membri</span>
                </a>
                @endif

                @if(auth()->user()->hasSection('stati_famiglia'))
                <a href="{{ route('family-status.index') }}" class="{{ request()->routeIs('family-status.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Stati Famiglia</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('libri-sociali.index') }}" class="{{ request()->routeIs('libri-sociali.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Libri Sociali</span>
                </a>
                @endif

                @if(auth()->user()->hasSection('documents'))
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.index') || request()->routeIs('documents.show') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Documenti</span>
                </a>
                <a href="{{ route('documents.expiring') }}" class="{{ request()->routeIs('documents.expiring') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Scadenze</span>
                </a>
                @if(auth()->user()->hasPermission('documents.upload'))
                <a href="{{ route('documents.create') }}" class="{{ request()->routeIs('documents.create') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Carica Documento</span>
                </a>
                @endif
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('email.index') }}" class="{{ request()->routeIs('email.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Email</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <div class="pt-3">
                    <p class="px-4 text-xs font-semibold text-brand-400 uppercase tracking-wider">Amministrazione</p>
                </div>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Utenti</span>
                </a>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Impostazioni</span>
                </a>
                @endif

                @if(auth()->user()->hasRole('admin', 'manager'))
                <a href="{{ route('activity-log.index') }}" class="{{ request()->routeIs('activity-log.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Registro Attivita</span>
                </a>
                @endif
            </nav>

            {{-- User info --}}
            <div class="border-t border-brand-800 px-4 py-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-gold-500 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 hover:bg-gold-400 transition" title="Il mio profilo">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-white truncate block hover:text-gold-300 transition">{{ auth()->user()->name }}</a>
                        <p class="text-xs text-brand-400 truncate">{{ auth()->user()->roles->first()?->label ?? 'Utente' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Esci" class="text-brand-400 hover:text-red-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top navbar --}}
            <header class="bg-white shadow-sm border-b border-gray-200 z-30">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        {{-- Breadcrumb --}}
                        <nav class="hidden sm:flex items-center gap-1.5 text-sm text-gray-400">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center text-gray-400 hover:text-brand-600 transition-colors"
                               title="Dashboard">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </a>
                            @hasSection('breadcrumb')
                            @yield('breadcrumb')
                            @endif
                        </nav>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Global Search --}}
                        <div class="hidden md:block relative" x-data="{ open: false, query: '', results: [] }">
                            <input type="text" x-model="query" placeholder="Cerca societa, documenti..." class="form-input w-64 pl-9 text-sm" @focus="open = true" @click.outside="open = false">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        {{-- Notification Bell --}}
                        <div class="relative" x-data="notificationBell()">
                            <button @click="toggle()" class="relative text-gray-500 hover:text-gray-700 p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span x-show="count > 0" x-text="count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
                            </button>
                            <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifiche</h3>
                                    <button @click="markAllAsRead()" class="text-xs text-brand-600 hover:underline">Segna tutte lette</button>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <a href="{{ route('notifications.index') }}" class="block px-4 py-3 text-sm text-gray-600 hover:bg-gray-50">
                                        Vedi tutte le notifiche
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mx-4 mt-4 lg:mx-6">
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mx-4 mt-4 lg:mx-6">
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
