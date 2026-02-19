<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficerRequest;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\CompanyOfficer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyOfficerController extends Controller
{
    public function store(StoreOfficerRequest $request, Company $company): RedirectResponse
    {
        $officer = $company->officers()->create($request->validated());
        $officer->load('member');

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'model_type' => CompanyOfficer::class,
            'model_id' => $officer->id,
            'description' => "Aggiunta carica: {$officer->member->full_name} - {$officer->ruolo} ({$company->denominazione})",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('companies.show', ['company' => $company, '#cariche'])
            ->with('success', 'Carica societaria aggiunta con successo.');
    }

    public function update(StoreOfficerRequest $request, CompanyOfficer $officer): RedirectResponse
    {
        $officer->update($request->validated());
        $officer->load('member');

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => CompanyOfficer::class,
            'model_id' => $officer->id,
            'description' => "Modificata carica: {$officer->member->full_name} - {$officer->ruolo}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('companies.show', ['company' => $officer->company_id, '#cariche'])
            ->with('success', 'Carica societaria aggiornata.');
    }

    public function destroy(Request $request, CompanyOfficer $officer): RedirectResponse
    {
        $companyId = $officer->company_id;
        $officer->load('member');
        $fullName = $officer->member->full_name;
        $officer->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'model_type' => CompanyOfficer::class,
            'model_id' => $officer->id,
            'description' => "Rimossa carica: {$fullName}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('companies.show', ['company' => $companyId, '#cariche'])
            ->with('success', 'Carica societaria rimossa.');
    }
}
