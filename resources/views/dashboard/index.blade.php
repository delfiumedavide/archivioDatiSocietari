@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="text-gray-400">/</span>
    <a href="{{ route('dashboard') }}" class="text-brand-600 font-medium">Home</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Page Title --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Panoramica generale dell'archivio documentale</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Societa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Societa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_companies']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Documenti --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Documenti</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_documents']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- In Scadenza --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">In Scadenza</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['expiring_count']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Scaduti --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Scaduti</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['expired_count']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Expiring Documents Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Documenti in Scadenza</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ordinati per urgenza</p>
            </div>
            <a href="{{ route('documents.expiring') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                Vedi tutti
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Societa</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Scadenza</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stato</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expiringDocuments as $document)
                        <tr class="{{ $document->computed_status === 'expired' ? 'bg-red-50' : ($document->computed_status === 'expiring' ? 'bg-yellow-50' : '') }} hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                <a href="{{ route('documents.show', $document) }}" class="text-brand-600 hover:text-brand-700 font-medium">
                                    {{ $document->title }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $document->company?->denominazione ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $document->category?->label ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $document->expiration_date?->format('d/m/Y') ?? '-' }}
                                <span class="block text-xs {{ $document->days_until_expiration < 0 ? 'text-red-500' : 'text-yellow-600' }}">
                                    @if($document->days_until_expiration < 0)
                                        Scaduto da {{ abs($document->days_until_expiration) }} giorni
                                    @elseif($document->days_until_expiration === 0)
                                        Scade oggi
                                    @else
                                        Tra {{ $document->days_until_expiration }} giorni
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $document->computed_status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $document->computed_status === 'expiring' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $document->computed_status === 'valid' ? 'bg-green-100 text-green-800' : '' }}
                                ">
                                    {{ $document->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="font-medium">Nessun documento in scadenza</p>
                                <p class="text-sm mt-1">Tutti i documenti sono in regola.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Doughnut: Documents by Category --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documenti per Categoria</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="chartByCategory"></canvas>
            </div>
        </div>

        {{-- Bar: Documents by Company --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documenti per Societa</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="chartByCompany"></canvas>
            </div>
        </div>

        {{-- Pie: Expiration Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Stato Scadenze</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="chartExpirationStatus"></canvas>
            </div>
        </div>

        {{-- Line: Upload Activity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attivita di Caricamento (12 mesi)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="chartUploadActivity"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Attivita Recenti</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ultime operazioni effettuate</p>
            </div>
            <a href="{{ route('activity-log.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                Vedi tutte
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentActivity as $activity)
                <div class="px-6 py-3 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    {{-- Activity Color Indicator --}}
                    <div class="w-2 h-2 rounded-full flex-shrink-0 bg-{{ $activity->action_color }}-500"></div>
                    {{-- Details --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">{{ $activity->user->name }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-{{ $activity->action_color }}-100 text-{{ $activity->action_color }}-700 mx-1">
                                {{ $activity->action_label }}
                            </span>
                            <span class="text-gray-600">{{ $activity->description }}</span>
                        </p>
                    </div>
                    {{-- Timestamp --}}
                    <time class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap">
                        {{ $activity->created_at->diffForHumans() }}
                    </time>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="font-medium">Nessuna attivita recente</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Chart.js Initialization --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const Chart = window.Chart;
    if (!Chart) return;

    // Brand color palette
    const brandPrimary = '#1e3a5f';
    const brandGold = '#c9952b';
    const brandPalette = [
        '#1e3a5f', '#c9952b', '#2563eb', '#7c3aed', '#059669',
        '#dc2626', '#d97706', '#0891b2', '#4f46e5', '#be185d',
        '#65a30d', '#ea580c'
    ];

    // Common options
    const defaultFont = { family: "'Inter', 'Segoe UI', sans-serif" };
    Chart.defaults.font = defaultFont;
    Chart.defaults.color = '#6b7280';

    // --- Doughnut: Documents by Category ---
    const categoryData = @json($documentsByCategory);
    new Chart(document.getElementById('chartByCategory'), {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.values,
                backgroundColor: brandPalette.slice(0, categoryData.labels.length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 }
                }
            }
        }
    });

    // --- Bar: Documents by Company ---
    const companyData = @json($documentsByCompany);
    new Chart(document.getElementById('chartByCompany'), {
        type: 'bar',
        data: {
            labels: companyData.labels,
            datasets: [{
                label: 'Documenti',
                data: companyData.values,
                backgroundColor: brandPrimary + 'cc',
                borderColor: brandPrimary,
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // --- Pie: Expiration Status ---
    const expirationData = @json($expirationData);
    new Chart(document.getElementById('chartExpirationStatus'), {
        type: 'pie',
        data: {
            labels: expirationData.labels,
            datasets: [{
                data: expirationData.values,
                backgroundColor: ['#059669', '#d97706', '#dc2626'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 }
                }
            }
        }
    });

    // --- Line: Upload Activity (12 months) ---
    const uploadData = @json($uploadActivity);
    new Chart(document.getElementById('chartUploadActivity'), {
        type: 'line',
        data: {
            labels: uploadData.labels,
            datasets: [{
                label: 'Documenti caricati',
                data: uploadData.values,
                borderColor: brandGold,
                backgroundColor: brandGold + '20',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointBackgroundColor: brandGold,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endsection
