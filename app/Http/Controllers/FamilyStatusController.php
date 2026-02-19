<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FamilyMember;
use App\Models\FamilyStatusChange;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FamilyStatusController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::with([
                'familyStatusChanges' => fn ($q) => $q->orderByDesc('data_variazione')->limit(1),
            ])
            ->withCount(['familyMembers' => fn ($q) => $q->active()])
            ->when($request->input('search'), fn ($q, $v) => $q->search($v))
            ->when($request->input('stato_civile'), function ($q, $stato) {
                $q->whereHas('familyStatusChanges', function ($sub) use ($stato) {
                    $sub->where('stato_civile', $stato)
                        ->whereRaw('data_variazione = (SELECT MAX(data_variazione) FROM family_status_changes WHERE family_status_changes.member_id = members.id)');
                });
            })
            ->orderBy('cognome')
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        return view('family-status.index', compact('members'));
    }

    public function show(Member $member): View
    {
        $member->load([
            'familyStatusChanges' => fn ($q) => $q->with('registeredBy')->orderByDesc('data_variazione'),
            'familyMembers',
        ]);

        $activeFamilyMembers = $member->familyMembers->whereNull('data_fine');
        $inactiveFamilyMembers = $member->familyMembers->whereNotNull('data_fine');

        return view('family-status.show', compact('member', 'activeFamilyMembers', 'inactiveFamilyMembers'));
    }

    public function storeStatusChange(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'stato_civile' => ['required', 'string', 'max:50'],
            'data_variazione' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $change = $member->familyStatusChanges()->create([
            ...$validated,
            'registered_by' => $request->user()->id,
        ]);

        $member->update(['stato_civile' => $validated['stato_civile']]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'model_type' => FamilyStatusChange::class,
            'model_id' => $change->id,
            'description' => "Variazione stato civile: {$member->full_name} - {$validated['stato_civile']}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('family-status.show', $member)
            ->with('success', 'Variazione stato civile registrata.');
    }

    public function storeFamilyMember(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['nullable', 'string', 'max:16'],
            'relazione' => ['required', 'string', 'max:50'],
            'data_nascita' => ['nullable', 'date'],
            'luogo_nascita' => ['nullable', 'string', 'max:100'],
            'data_inizio' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $familyMember = $member->familyMembers()->create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'model_type' => FamilyMember::class,
            'model_id' => $familyMember->id,
            'description' => "Aggiunto componente nucleo: {$familyMember->full_name} ({$validated['relazione']}) a {$member->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('family-status.show', $member)
            ->with('success', 'Componente nucleo familiare aggiunto.');
    }

    public function updateFamilyMember(Request $request, FamilyMember $familyMember): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'codice_fiscale' => ['nullable', 'string', 'max:16'],
            'relazione' => ['required', 'string', 'max:50'],
            'data_nascita' => ['nullable', 'date'],
            'luogo_nascita' => ['nullable', 'string', 'max:100'],
            'data_inizio' => ['nullable', 'date'],
            'data_fine' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $familyMember->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => FamilyMember::class,
            'model_id' => $familyMember->id,
            'description' => "Modificato componente nucleo: {$familyMember->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('family-status.show', $familyMember->member_id)
            ->with('success', 'Componente nucleo familiare aggiornato.');
    }

    public function destroyFamilyMember(Request $request, FamilyMember $familyMember): RedirectResponse
    {
        $memberId = $familyMember->member_id;
        $fullName = $familyMember->full_name;

        $familyMember->update(['data_fine' => now()]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => FamilyMember::class,
            'model_id' => $familyMember->id,
            'description' => "Rimosso dal nucleo: {$fullName}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('family-status.show', $memberId)
            ->with('success', 'Componente rimosso dal nucleo familiare.');
    }
}
