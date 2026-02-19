<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\ActivityLog;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::withCount(['officers' => fn ($q) => $q->active()])
            ->when($request->input('search'), fn ($q, $v) => $q->search($v))
            ->when($request->input('white_list') !== null, function ($q) use ($request) {
                $q->where('white_list', $request->boolean('white_list'));
            })
            ->orderBy('cognome')
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = Member::create($request->validated());

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'created',
            'model_type' => Member::class,
            'model_id' => $member->id,
            'description' => "Creato membro: {$member->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', 'Membro creato con successo.');
    }

    public function show(Member $member): View
    {
        $member->load([
            'officers' => fn ($q) => $q->with('company')->orderByDesc('data_nomina'),
            'documents' => fn ($q) => $q->with('category')->latest()->limit(10),
            'familyStatusChanges' => fn ($q) => $q->with('registeredBy'),
            'familyMembers' => fn ($q) => $q->active(),
        ]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(StoreMemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validated());

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => Member::class,
            'model_id' => $member->id,
            'description' => "Modificato membro: {$member->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', 'Membro aggiornato con successo.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        if (!$request->user()->hasPermission('membri.delete')) {
            abort(403);
        }

        $fullName = $member->full_name;
        $member->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'model_type' => Member::class,
            'model_id' => $member->id,
            'description' => "Eliminato membro: {$fullName}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Membro eliminato.');
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $members = Member::active()
            ->search($term)
            ->limit(10)
            ->get(['id', 'nome', 'cognome', 'codice_fiscale'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'full_name' => $m->full_name,
                'codice_fiscale' => $m->codice_fiscale,
            ]);

        return response()->json($members);
    }
}
