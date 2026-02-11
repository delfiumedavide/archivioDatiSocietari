@extends('layouts.app')
@section('title', 'Notifiche')
@section('breadcrumb')
<span class="text-gray-400">/</span>
<span class="text-gray-700 font-medium">Notifiche</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Notifiche</h1>
        @if($notifications->where('read_at', null)->isNotEmpty())
        <form method="POST" action="{{ route('notifications.readAll') }}" class="inline">
            @csrf
            <button type="submit" class="btn-secondary text-sm">Segna tutte come lette</button>
        </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
        <div class="card {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-brand-500' }}">
            <div class="card-body flex items-start gap-4">
                <div class="flex-shrink-0 mt-0.5">
                    @if(($notification->data['type'] ?? '') === 'expired')
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </div>
                    @else
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'Notifica' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $notification->data['company'] ?? '' }} &middot; {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                @if(!$notification->read_at)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="text-xs text-brand-600 hover:underline">Segna letta</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-12">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-gray-500">Nessuna notifica</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
