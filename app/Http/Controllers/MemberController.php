<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\MemberDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    private string $disk = 'documents';

    public function index(Request $request): View
    {
        $members = Member::query()
            ->with('documents')
            ->withCount('officers')
            ->when($request->input('search'), function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('cognome', 'like', "%{$search}%")
                        ->orWhere('codice_fiscale', 'like', "%{$search}%");
                });
            })
            ->orderBy('cognome')
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $memberData = $request->safe()->except([
            'documento_identita_file',
            'documento_identita_scadenza',
            'codice_fiscale_file',
            'codice_fiscale_scadenza',
        ]);

        $member = Member::create($memberData);

        $this->upsertDocument(
            member: $member,
            type: 'documento_identita',
            file: $request->file('documento_identita_file'),
            expirationDate: $request->input('documento_identita_scadenza'),
            userId: $request->user()->id
        );

        $this->upsertDocument(
            member: $member,
            type: 'codice_fiscale',
            file: $request->file('codice_fiscale_file'),
            expirationDate: $request->input('codice_fiscale_scadenza'),
            userId: $request->user()->id
        );

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

        return redirect()->route('members.index')
            ->with('success', 'Membro creato con successo.');
    }

    public function edit(Member $member): View
    {
        $member->load('documents');

        return view('members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $memberData = $request->safe()->except([
            'documento_identita_file',
            'documento_identita_scadenza',
            'codice_fiscale_file',
            'codice_fiscale_scadenza',
        ]);

        $member->update($memberData);

        $this->upsertDocument(
            member: $member,
            type: 'documento_identita',
            file: $request->file('documento_identita_file'),
            expirationDate: $request->input('documento_identita_scadenza'),
            userId: $request->user()->id
        );

        $this->upsertDocument(
            member: $member,
            type: 'codice_fiscale',
            file: $request->file('codice_fiscale_file'),
            expirationDate: $request->input('codice_fiscale_scadenza'),
            userId: $request->user()->id
        );

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'model_type' => Member::class,
            'model_id' => $member->id,
            'description' => "Aggiornato membro: {$member->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Membro aggiornato con successo.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        foreach ($member->documents as $document) {
            Storage::disk($this->disk)->delete($document->file_path);
            $document->delete();
        }

        $name = $member->full_name;
        $member->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'model_type' => Member::class,
            'model_id' => $member->id,
            'description' => "Eliminato membro: {$name}",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Membro eliminato.');
    }

    public function downloadDocument(Request $request, Member $member, string $type): StreamedResponse
    {
        if (!in_array($type, ['documento_identita', 'codice_fiscale'], true)) {
            abort(404);
        }

        $document = $member->documents()->where('type', $type)->firstOrFail();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'downloaded',
            'model_type' => MemberDocument::class,
            'model_id' => $document->id,
            'description' => "Scaricato documento membro: {$member->full_name} ({$document->type_label})",
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return Storage::disk($this->disk)->download($document->file_path, $document->file_name_original);
    }

    private function upsertDocument(Member $member, string $type, ?UploadedFile $file, ?string $expirationDate, int $userId): void
    {
        $document = $member->documents()->where('type', $type)->first();

        if (!$document && !$file) {
            return;
        }

        if ($document && !$file) {
            $document->update(['expiration_date' => $expirationDate]);

            return;
        }

        $hashedName = hash('sha256', $file->getClientOriginalName() . time() . Str::random(16));
        $extension = $file->getClientOriginalExtension();
        $year = date('Y');
        $path = "members/{$member->id}/{$type}/{$year}/{$hashedName}.{$extension}";

        Storage::disk($this->disk)->putFileAs(dirname($path), $file, basename($path));

        if ($document) {
            Storage::disk($this->disk)->delete($document->file_path);

            $document->update([
                'file_path' => $path,
                'file_name_original' => $file->getClientOriginalName(),
                'file_mime_type' => (string) $file->getMimeType(),
                'file_size' => $file->getSize(),
                'expiration_date' => $expirationDate,
                'uploaded_by' => $userId,
            ]);

            return;
        }

        $member->documents()->create([
            'type' => $type,
            'file_path' => $path,
            'file_name_original' => $file->getClientOriginalName(),
            'file_mime_type' => (string) $file->getMimeType(),
            'file_size' => $file->getSize(),
            'expiration_date' => $expirationDate,
            'uploaded_by' => $userId,
        ]);
    }
}
