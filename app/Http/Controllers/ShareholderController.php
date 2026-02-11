<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Shareholder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShareholderController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:persona_fisica,persona_giuridica'],
            'nome' => ['required', 'string', 'max:255'],
            'codice_fiscale' => ['nullable', 'string', 'max:16'],
            'quota_percentuale' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'quota_valore' => ['nullable', 'numeric', 'min:0'],
            'data_ingresso' => ['nullable', 'date'],
            'data_uscita' => ['nullable', 'date', 'after:data_ingresso'],
            'diritti_voto' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $shareholder = $company->shareholders()->create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'model_type' => Shareholder::class,
            'model_id' => $shareholder->id,
            'description' => "Aggiunto socio: {$shareholder->nome} ({$company->denominazione})",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('companies.show', ['company' => $company, '#soci'])
            ->with('success', 'Socio aggiunto con successo.');
    }

    public function update(Request $request, Shareholder $shareholder): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:persona_fisica,persona_giuridica'],
            'nome' => ['required', 'string', 'max:255'],
            'codice_fiscale' => ['nullable', 'string', 'max:16'],
            'quota_percentuale' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'quota_valore' => ['nullable', 'numeric', 'min:0'],
            'data_ingresso' => ['nullable', 'date'],
            'data_uscita' => ['nullable', 'date', 'after:data_ingresso'],
            'diritti_voto' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $shareholder->update($validated);

        return redirect()->route('companies.show', ['company' => $shareholder->company_id, '#soci'])
            ->with('success', 'Socio aggiornato.');
    }

    public function destroy(Request $request, Shareholder $shareholder): RedirectResponse
    {
        $companyId = $shareholder->company_id;
        $shareholder->delete();

        return redirect()->route('companies.show', ['company' => $companyId, '#soci'])
            ->with('success', 'Socio rimosso.');
    }
}
