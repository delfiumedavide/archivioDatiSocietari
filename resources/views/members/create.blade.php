@extends('layouts.app')

@section('title', 'Nuovo Membro')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Home</a>
    <span class="mx-2">/</span>
    <a href="{{ route('members.index') }}" class="hover:text-brand-600">Membri</a>
    <span class="mx-2">/</span>
    <span class="text-gray-700 font-medium">Nuovo</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Nuovo Membro</h1>
        <a href="{{ route('members.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Annulla</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
            @csrf

            @include('members._form')

            <div class="mt-8 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg transition text-sm">
                    Salva Membro
                </button>
                <a href="{{ route('members.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
