<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyRelationshipController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($company), 403);

        $validated = $request->validate([
            'child_company_id'  => ['required', 'integer', 'exists:companies,id', 'different:company'],
            'relationship_type' => ['required', 'string', 'max:100'],
            'quota_percentuale' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'data_inizio'       => ['nullable', 'date'],
            'data_fine'         => ['nullable', 'date', 'after_or_equal:data_inizio'],
            'note'              => ['nullable', 'string', 'max:1000'],
        ], [
            'child_company_id.different' => 'La società controllata non può essere la stessa società.',
        ]);

        $validated['parent_company_id'] = $company->id;

        CompanyRelationship::create($validated);

        return redirect()->route('companies.show', [$company, '#relazioni'])
            ->with('success', 'Relazione aggiunta con successo.');
    }

    public function destroy(Request $request, CompanyRelationship $relationship): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($relationship->parent_company_id), 403);

        $parentId = $relationship->parent_company_id;
        $relationship->delete();

        return redirect()->route('companies.show', [$parentId, '#relazioni'])
            ->with('success', 'Relazione eliminata.');
    }
}
