@extends('layouts.app')

@section('title', 'Email')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ request('tab', 'config') }}', anno: {{ $anno }} }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email</h1>
            <p class="text-sm text-gray-500 mt-1">Gestione promemoria scadenze e invio dichiarazioni stato famiglia</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @foreach(['success','error','warning','info'] as $type)
        @if(session($type))
            <div class="rounded-lg p-4 flex items-start gap-3
                @if($type === 'success') bg-green-50 border border-green-200 text-green-800
                @elseif($type === 'error') bg-red-50 border border-red-200 text-red-800
                @elseif($type === 'warning') bg-yellow-50 border border-yellow-200 text-yellow-800
                @else bg-blue-50 border border-blue-200 text-blue-800 @endif">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session($type) }}</span>
            </div>
        @endif
    @endforeach

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6">
            <button @click="tab='config'" :class="tab==='config' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Configurazione
            </button>
            <button @click="tab='scadenze'" :class="tab==='scadenze' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Memo Scadenze
                @if($expiringCount + $expiredCount > 0)
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $expiringCount + $expiredCount }}</span>
                @endif
            </button>
            <button @click="tab='dichiarazioni'" :class="tab==='dichiarazioni' ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Dichiarazioni Stato Famiglia
            </button>
        </nav>
    </div>

    {{-- ===================== TAB: CONFIGURAZIONE ===================== --}}
    <div x-show="tab==='config'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form configurazione --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Configurazione Promemoria</h2>
                    <p class="text-sm text-gray-500 mb-5">Imposta i destinatari, i giorni di anticipo e l'orario di invio automatico giornaliero.</p>

                    <form action="{{ route('email.update-settings') }}" method="POST"
                          x-data="{ enabled: {{ $settings->expiry_reminder_enabled ?? true ? 'true' : 'false' }} }">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            {{-- Toggle invio automatico --}}
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Invio Automatico Giornaliero</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Abilita l'invio automatico della memo scadenze ogni giorno all'orario impostato.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="expiry_reminder_enabled" value="0">
                                    <input type="checkbox" name="expiry_reminder_enabled" value="1"
                                        x-model="enabled"
                                        {{ old('expiry_reminder_enabled', $settings->expiry_reminder_enabled ?? true) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                                </label>
                            </div>

                            {{-- Orario invio --}}
                            <div x-show="enabled" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Orario Invio Automatico</label>
                                <div class="flex items-center gap-3">
                                    <input type="time" name="expiry_reminder_time"
                                        value="{{ old('expiry_reminder_time', $settings->expiry_reminder_time ?? '08:00') }}"
                                        class="form-input w-36">
                                    <span class="text-sm text-gray-500">ogni giorno</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Modifica richiede il riavvio del processo scheduler.</p>
                            </div>

                            {{-- Indirizzi destinatari --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzi Email Destinatari</label>
                                <textarea name="notification_emails" rows="5"
                                    class="form-input font-mono text-sm"
                                    placeholder="esempio@email.com&#10;altro@email.com">{{ old('notification_emails', $settings->notification_emails) }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Un indirizzo per riga. Riceveranno sia l'invio automatico che quello manuale.</p>
                            </div>

                            {{-- Giorni anticipo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giorni Anticipo Promemoria</label>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="expiry_reminder_days" min="1" max="365"
                                        value="{{ old('expiry_reminder_days', $settings->expiry_reminder_days ?? 30) }}"
                                        class="form-input w-32">
                                    <span class="text-sm text-gray-500">giorni prima della scadenza</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">I documenti in scadenza entro questo numero di giorni verranno inclusi nella memo.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="btn-primary">
                                Salva Configurazione
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info SMTP --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Configurazione SMTP
                    </h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Driver</dt>
                            <dd class="font-mono font-medium text-gray-800">{{ config('mail.default', 'N/D') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Host</dt>
                            <dd class="font-mono font-medium text-gray-800">{{ config('mail.mailers.smtp.host', 'N/D') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Porta</dt>
                            <dd class="font-mono font-medium text-gray-800">{{ config('mail.mailers.smtp.port', 'N/D') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Crittografia</dt>
                            <dd class="font-mono font-medium text-gray-800">{{ config('mail.mailers.smtp.encryption', 'nessuna') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Mittente</dt>
                            <dd class="font-mono font-medium text-gray-800 truncate max-w-32">{{ config('mail.from.address', 'N/D') }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-700">
                            Configura SMTP tramite variabili d'ambiente:<br>
                            <code class="font-mono">MAIL_HOST</code>, <code class="font-mono">MAIL_PORT</code>,<br>
                            <code class="font-mono">MAIL_USERNAME</code>, <code class="font-mono">MAIL_PASSWORD</code>
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Invio Automatico</h3>
                    @if($settings->expiry_reminder_enabled ?? true)
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs font-medium text-green-700">Attivo</span>
                        </div>
                        <p class="text-xs text-gray-500">La memo viene inviata ogni giorno alle <strong>{{ $settings->expiry_reminder_time ?? '08:00' }}</strong> se ci sono documenti da segnalare.</p>
                    @else
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span class="text-xs font-medium text-gray-500">Disabilitato</span>
                        </div>
                        <p class="text-xs text-gray-500">L'invio automatico è disabilitato. Attivalo dalla scheda <button type="button" @click="tab='config'" class="underline text-brand-600">Configurazione</button>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== TAB: MEMO SCADENZE ===================== --}}
    <div x-show="tab==='scadenze'" x-transition>
        <div class="space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-yellow-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-yellow-700">{{ $expiringCount }}</p>
                            <p class="text-sm text-gray-500">in scadenza entro {{ $days }} giorni</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-red-700">{{ $expiredCount }}</p>
                            <p class="text-sm text-gray-500">già scaduti</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Anteprima documenti --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-900">Anteprima — Prossimi in Scadenza</h2>
                    </div>
                    @if($expiring->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-medium">Titolo</th>
                                        <th class="px-6 py-3 text-left font-medium">Intestatario</th>
                                        <th class="px-6 py-3 text-left font-medium">Scadenza</th>
                                        <th class="px-6 py-3 text-left font-medium">Giorni</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($expiring as $doc)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $doc->title }}</td>
                                            <td class="px-6 py-3 text-gray-600">{{ $doc->owner_name }}</td>
                                            <td class="px-6 py-3 text-gray-600">{{ $doc->expiration_date?->format('d/m/Y') }}</td>
                                            <td class="px-6 py-3">
                                                @if($doc->days_until_expiration !== null)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ $doc->days_until_expiration }} gg
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($expiringCount > 10)
                            <div class="px-6 py-3 text-xs text-gray-400 border-t border-gray-50">
                                Mostrati i primi 10 di {{ $expiringCount }} documenti in scadenza.
                            </div>
                        @endif
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-400">Nessun documento in scadenza nei prossimi {{ $days }} giorni.</p>
                        </div>
                    @endif
                </div>

                {{-- Azione invio --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Invia Memo Ora</h3>
                        <p class="text-xs text-gray-500 mb-4">
                            Invia subito l'email promemoria con il riepilogo di tutti i documenti in scadenza e scaduti agli indirizzi configurati.
                        </p>
                        @if(empty(trim($settings->notification_emails ?? '')))
                            <div class="p-3 bg-yellow-50 rounded-lg text-xs text-yellow-700 mb-4">
                                Nessun indirizzo configurato. Aggiungilo nella scheda Configurazione.
                            </div>
                        @endif
                        <form action="{{ route('email.send-expiry-reminder') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary w-full justify-center"
                                @if(empty(trim($settings->notification_emails ?? ''))) disabled @endif>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Invia Memo Ora
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Invio Automatico</h3>
                        @if($settings->expiry_reminder_enabled ?? true)
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-xs font-medium text-green-700">Attivo — ore {{ $settings->expiry_reminder_time ?? '08:00' }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-2 h-2 bg-gray-400 rounded-full"></span>
                                <span class="text-xs font-medium text-gray-500">Disabilitato</span>
                            </div>
                        @endif
                        <p class="text-xs text-gray-500">
                            Configura orario e destinatari nella scheda <button type="button" @click="tab='config'" class="underline text-brand-600">Configurazione</button>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== TAB: DICHIARAZIONI ===================== --}}
    <div x-show="tab==='dichiarazioni'" x-transition>
        <form action="{{ route('email.send-declarations') }}" method="POST" x-data="{ selectedCount: 0, selectAll: false }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Lista membri --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Selettore anno --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center gap-4">
                            <label class="text-sm font-medium text-gray-700">Anno Dichiarazioni:</label>
                            <select name="anno" x-model="anno"
                                @change="window.location.href='{{ route('email.index') }}?tab=dichiarazioni&anno=' + anno"
                                class="form-input w-32">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $anno ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <span class="text-sm text-gray-500">Seleziona i membri a cui inviare la dichiarazione PDF.</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-base font-semibold text-gray-900">Soci</h2>
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" x-model="selectAll"
                                    @change="document.querySelectorAll('.member-check:not(:disabled)').forEach(c => c.checked = selectAll); selectedCount = selectAll ? document.querySelectorAll('.member-check:not(:disabled)').length : 0"
                                    class="rounded border-gray-300 text-brand-600">
                                Seleziona tutti
                            </label>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($members as $member)
                                @php
                                    $decl = $declarations->get($member->id);
                                    $hasPdf = $decl && $decl->generated_path;
                                    $hasEmail = !empty($member->email);
                                    $canSend = $hasEmail && $hasPdf;
                                @endphp
                                <label class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 {{ $canSend ? 'cursor-pointer' : 'opacity-60 cursor-not-allowed' }}">
                                    <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                        class="member-check rounded border-gray-300 text-brand-600"
                                        {{ !$canSend ? 'disabled' : '' }}
                                        @change="selectedCount = document.querySelectorAll('.member-check:checked').length">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->email ?: '—' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if(!$hasEmail)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Nessuna email</span>
                                        @endif
                                        @if($hasPdf)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">PDF disponibile</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">PDF mancante</span>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div class="px-6 py-10 text-center text-sm text-gray-400">Nessun socio attivo trovato.</div>
                            @endforelse
                        </div>

                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500">
                            <span x-text="selectedCount"></span> selezionati
                        </div>
                    </div>
                </div>

                {{-- Form composizione email --}}
                <div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Componi Email</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Oggetto</label>
                                <input type="text" name="subject"
                                    value="{{ old('subject', 'Dichiarazione Stato di Famiglia - Anno ' . $anno) }}"
                                    class="form-input" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Corpo del Messaggio</label>
                                <textarea name="body" rows="10" class="form-input text-sm" required>{{ old('body', "Gentile Socio/a,\n\nin allegato troverà la Dichiarazione dello Stato di Famiglia per l'anno " . $anno . ".\n\nLa preghiamo di stampare il documento, firmarlo e restituirne una copia all'ufficio amministrativo.\n\nCordiali saluti,\nUfficio Amministrativo") }}</textarea>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn-primary w-full justify-center"
                                :disabled="selectedCount === 0"
                                :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Invia ai Selezionati (<span x-text="selectedCount"></span>)
                            </button>
                        </div>

                        <p class="text-xs text-gray-400 mt-3 text-center">
                            Il PDF sarà allegato automaticamente. Solo i soci con email e PDF disponibile possono essere selezionati.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
