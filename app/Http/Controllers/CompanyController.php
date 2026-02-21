<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\ActivityLog;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('companies');

        $companies = Company::query()
            ->forUser($user)
            ->search($request->input('search'))
            ->when($request->input('forma_giuridica'), fn ($q, $v) => $q->where('forma_giuridica', $v))
            ->when($request->input('status') === 'active', fn ($q) => $q->active())
            ->when($request->input('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->withCount('documents')
            ->orderBy('denominazione')
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());

        ActivityLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'created',
            'model_type'  => Company::class,
            'model_id'    => $company->id,
            'description' => "Creata società: {$company->denominazione}",
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            'created_at'  => now(),
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Società creata con successo.');
    }

    public function show(Request $request, Company $company): View
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($company), 403);

        $company->load([
            'officers'                    => fn ($q) => $q->with('member')->active()->orderBy('ruolo'),
            'shareholders'                => fn ($q) => $q->active()->orderByDesc('quota_percentuale'),
            'documents'                   => fn ($q) => $q->with('category')->latest()->limit(10),
            'childRelationships.childCompany',
            'parentRelationships.parentCompany',
        ]);

        $ceasedOfficers = $company->officers()
            ->with('member')
            ->whereNotNull('data_cessazione')
            ->orderByDesc('data_cessazione')
            ->get();

        $otherCompanies = Company::active()
            ->where('id', '!=', $company->id)
            ->orderBy('denominazione')
            ->get(['id', 'denominazione']);

        return view('companies.show', compact('company', 'ceasedOfficers', 'otherCompanies'));
    }

    public function edit(Request $request, Company $company): View
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($company), 403);

        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->load('companies')->canAccessCompany($company), 403);

        $oldValues = $company->toArray();
        $company->update($request->validated());

        ActivityLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'updated',
            'model_type'  => Company::class,
            'model_id'    => $company->id,
            'description' => "Modificata società: {$company->denominazione}",
            'properties'  => ['old' => $oldValues, 'new' => $company->fresh()->toArray()],
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            'created_at'  => now(),
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Società aggiornata con successo.');
    }

    public function destroy(Request $request, Company $company): RedirectResponse
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $denominazione = $company->denominazione;
        $company->delete();

        ActivityLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'deleted',
            'model_type'  => Company::class,
            'model_id'    => $company->id,
            'description' => "Eliminata società: {$denominazione}",
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            'created_at'  => now(),
        ]);

        return redirect()->route('companies.index')
            ->with('success', 'Società eliminata con successo.');
    }
}
