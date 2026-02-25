<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficerRequest;
use App\Models\Company;
use App\Models\CompanyOfficer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyOfficerController extends Controller
{
    public function store(StoreOfficerRequest $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($company), 403);

        $officer = $company->officers()->create($request->validated());
        $officer->load('member');

        $this->logActivity($request, 'created', "Aggiunta carica: {$officer->member->full_name} - {$officer->ruolo} ({$company->denominazione})", CompanyOfficer::class, $officer->id);

        return redirect()->route('companies.show', ['company' => $company, '#cariche'])
            ->with('success', 'Carica societaria aggiunta con successo.');
    }

    public function update(StoreOfficerRequest $request, CompanyOfficer $officer): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($officer->company_id), 403);

        $officer->update($request->validated());
        $officer->load('member');

        $this->logActivity($request, 'updated', "Modificata carica: {$officer->member->full_name} - {$officer->ruolo}", CompanyOfficer::class, $officer->id);

        return redirect()->route('companies.show', ['company' => $officer->company_id, '#cariche'])
            ->with('success', 'Carica societaria aggiornata.');
    }

    public function cease(Request $request, CompanyOfficer $officer): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($officer->company_id), 403);

        $validated = $request->validate([
            'data_cessazione' => ['required', 'date', 'after_or_equal:' . $officer->data_nomina->format('Y-m-d')],
        ]);

        $officer->update(['data_cessazione' => $validated['data_cessazione']]);
        $officer->load('member');

        $this->logActivity($request, 'updated', "Cessata carica: {$officer->member->full_name} - {$officer->ruolo} (dal {$officer->data_cessazione->format('d/m/Y')})", CompanyOfficer::class, $officer->id);

        return redirect()->route('companies.show', ['company' => $officer->company_id, '#cariche'])
            ->with('success', 'Carica segnata come cessata.');
    }

    public function destroy(Request $request, CompanyOfficer $officer): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($officer->company_id), 403);

        $companyId = $officer->company_id;
        $officer->load('member');
        $fullName = $officer->member->full_name;
        $officer->delete();

        $this->logActivity($request, 'deleted', "Rimossa carica: {$fullName}", CompanyOfficer::class, $officer->id);

        return redirect()->route('companies.show', ['company' => $companyId, '#cariche'])
            ->with('success', 'Carica societaria rimossa.');
    }
}
