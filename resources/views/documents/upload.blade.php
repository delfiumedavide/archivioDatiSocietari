@extends('layouts.app')
@section('title', 'Carica Documento')
@section('breadcrumb')
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<a href="{{ route('documents.index') }}" class="text-brand-600 hover:underline">Documenti</a>
<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Carica</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Carica Documento</h1>

    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-6"
          x-data="{
              ownerType: '{{ old('member_id', $preselectedMemberId ?? '') ? 'member' : 'company' }}',
              companyCategories: {{ Js::from($companyCategories) }},
              memberCategories: {{ Js::from($memberCategories) }},
              get categories() {
                  return this.ownerType === 'member' ? this.memberCategories : this.companyCategories;
              }
          }">
        @csrf

        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Informazioni Documento</h2>
            </div>
            <div class="card-body space-y-4">

                {{-- Owner Type Toggle --}}
                <div>
                    <label class="form-label">Documento per *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="owner_type" value="company" x-model="ownerType" class="form-radio text-brand-600">
                            <span class="text-sm font-medium text-gray-700">Societa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="owner_type" value="member" x-model="ownerType" class="form-radio text-brand-600">
                            <span class="text-sm font-medium text-gray-700">Membro</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Company Select --}}
                    <div x-show="ownerType === 'company'" x-transition>
                        <label for="company_id" class="form-label">Societa *</label>
                        <select name="company_id" id="company_id" class="form-select" :required="ownerType === 'company'">
                            <option value="">Seleziona societa...</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', request('company_id')) == $company->id ? 'selected' : '' }}>{{ $company->denominazione }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Member Select --}}
                    <div x-show="ownerType === 'member'" x-transition>
                        <label for="member_id" class="form-label">Membro *</label>
                        <select name="member_id" id="member_id" class="form-select" :required="ownerType === 'member'">
                            <option value="">Seleziona membro...</option>
                            @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id', $preselectedMemberId ?? '') == $member->id ? 'selected' : '' }}>{{ $member->full_name }} ({{ $member->codice_fiscale }})</option>
                            @endforeach
                        </select>
                        @error('member_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Category (dynamic based on owner type) --}}
                    <div>
                        <label for="document_category_id" class="form-label">Categoria *</label>
                        <select name="document_category_id" id="document_category_id" class="form-select" required>
                            <option value="">Seleziona categoria...</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.label" :selected="cat.id == '{{ old('document_category_id') }}'"></option>
                            </template>
                        </select>
                        @error('document_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="title" class="form-label">Titolo *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-input" required placeholder="Es: Visura camerale 2024">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="form-label">Descrizione</label>
                    <textarea name="description" id="description" rows="3" class="form-input" placeholder="Descrizione opzionale...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="expiration_date" class="form-label">Data Scadenza</label>
                    <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date') }}" class="form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    <p class="text-xs text-gray-500 mt-1">Lascia vuoto se il documento non ha scadenza</p>
                    @error('expiration_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- File Upload --}}
        <div class="card" x-data="fileUpload()">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">File</h2>
            </div>
            <div class="card-body">
                <div
                    class="border-2 border-dashed rounded-xl p-8 text-center transition-colors"
                    :class="dragging ? 'border-brand-500 bg-brand-50' : 'border-gray-300 hover:border-brand-400'"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>

                    <template x-if="!fileName">
                        <div>
                            <p class="text-gray-700 font-medium">Trascina il file qui oppure</p>
                            <label for="file" class="inline-block mt-2 btn-secondary cursor-pointer">
                                Sfoglia file
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Formati: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, P7M - Max 50MB</p>
                        </div>
                    </template>

                    <template x-if="fileName">
                        <div>
                            <svg class="w-10 h-10 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-gray-900 font-medium" x-text="fileName"></p>
                            <p class="text-sm text-gray-500" x-text="fileSize"></p>
                            <label for="file" class="inline-block mt-2 text-sm text-brand-600 hover:underline cursor-pointer">Cambia file</label>
                        </div>
                    </template>

                    <input type="file" name="file" id="file" x-ref="fileInput" class="hidden" @change="handleSelect($event)" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.p7m">
                </div>
                @error('file')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('documents.index') }}" class="btn-secondary">Annulla</a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Carica Documento
            </button>
        </div>
    </form>
</div>
@endsection
