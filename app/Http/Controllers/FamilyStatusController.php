<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FamilyMember;
use App\Models\FamilyStatusChange;
use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use App\Services\DeclarationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FamilyStatusController extends Controller
{
    public function __construct(
        private DeclarationService $declarationService
    ) {}

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
            'declarations',
        ]);

        $activeFamilyMembers = $member->familyMembers->whereNull('data_fine');
        $inactiveFamilyMembers = $member->familyMembers->whereNotNull('data_fine');

        return view('family-status.show', compact('member', 'activeFamilyMembers', 'inactiveFamilyMembers'));
    }

    // ─── Declarations ───────────────────────────────────────────────

    public function declarations(Request $request): View
    {
        $year = (int) $request->input('anno', date('Y'));
        $filterSigned = $request->input('firma');
        $search = $request->input('search');

        $membersQuery = Member::active()
            ->when($search, fn ($q, $v) => $q->search($v))
            ->orderBy('cognome')
            ->orderBy('nome');

        $members = $membersQuery->get();
        $memberIds = $members->pluck('id');

        $declarations = FamilyStatusDeclaration::forYear($year)
            ->whereIn('member_id', $memberIds)
            ->get()
            ->keyBy('member_id');

        // Apply signature filter
        if ($filterSigned === 'firmati') {
            $signedMemberIds = $declarations->filter(fn ($d) => $d->is_signed)->keys();
            $members = $members->whereIn('id', $signedMemberIds);
        } elseif ($filterSigned === 'non_firmati') {
            $signedMemberIds = $declarations->filter(fn ($d) => $d->is_signed)->keys();
            $members = $members->whereNotIn('id', $signedMemberIds);
        }

        $stats = [
            'totale' => $members->count(),
            'generate' => $declarations->filter(fn ($d) => $d->is_generated)->count(),
            'firmate' => $declarations->filter(fn ($d) => $d->is_signed)->count(),
            'da_firmare' => $members->count() - $declarations->filter(fn ($d) => $d->is_signed)->count(),
        ];

        return view('family-status.declarations', compact('members', 'declarations', 'year', 'stats', 'filterSigned', 'search'));
    }

    public function generateDeclaration(Request $request, Member $member): RedirectResponse
    {
        $year = (int) $request->input('anno', date('Y'));

        $declaration = $this->declarationService->generate($member, $year, $request->user()->id);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'generated',
            'model_type' => FamilyStatusDeclaration::class,
            'model_id' => $declaration->id,
            'description' => "Generata dichiarazione stato famiglia: {$member->full_name} - Anno {$year}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        $redirectTo = $request->input('redirect', 'declarations');

        if ($redirectTo === 'show') {
            return redirect()->route('family-status.show', $member)
                ->with('success', "Dichiarazione {$year} generata.");
        }

        return redirect()->route('family-status.declarations', ['anno' => $year])
            ->with('success', "Dichiarazione generata per {$member->full_name}.");
    }

    public function downloadGenerated(FamilyStatusDeclaration $declaration): StreamedResponse
    {
        return $this->declarationService->downloadGenerated($declaration);
    }

    public function uploadSigned(Request $request, FamilyStatusDeclaration $declaration): RedirectResponse
    {
        $request->validate([
            'signed_file' => ['required', 'file', 'max:51200', 'mimes:pdf,p7m'],
        ]);

        $this->declarationService->storeSigned($declaration, $request->file('signed_file'));

        $member = $declaration->member;

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => FamilyStatusDeclaration::class,
            'model_id' => $declaration->id,
            'description' => "Caricata dichiarazione firmata: {$member->full_name} - Anno {$declaration->anno}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        $redirectTo = $request->input('redirect', 'declarations');

        if ($redirectTo === 'show') {
            return redirect()->route('family-status.show', $declaration->member_id)
                ->with('success', 'Dichiarazione firmata caricata.');
        }

        return redirect()->route('family-status.declarations', ['anno' => $declaration->anno])
            ->with('success', "Dichiarazione firmata caricata per {$member->full_name}.");
    }

    public function downloadSigned(FamilyStatusDeclaration $declaration): StreamedResponse
    {
        return $this->declarationService->downloadSigned($declaration);
    }

    public function bulkGenerate(Request $request): RedirectResponse
    {
        $year = (int) $request->input('anno', date('Y'));
        $members = Member::active()->get();
        $count = 0;

        foreach ($members as $member) {
            $this->declarationService->generate($member, $year, $request->user()->id);
            $count++;
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'generated',
            'model_type' => FamilyStatusDeclaration::class,
            'model_id' => 0,
            'description' => "Generazione massiva dichiarazioni: {$count} membri - Anno {$year}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('family-status.declarations', ['anno' => $year])
            ->with('success', "Generate {$count} dichiarazioni per l'anno {$year}.");
    }

    public function bulkDownload(Request $request): BinaryFileResponse
    {
        $year = (int) $request->input('anno', date('Y'));

        $zipPath = $this->declarationService->buildBulkZip($year);

        return response()->download($zipPath, "dichiarazioni_{$year}.zip")->deleteFileAfterSend(true);
    }

    // ─── Status Changes & Family Members ────────────────────────────

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
