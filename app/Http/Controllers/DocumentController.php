<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Company;
use App\Models\CompanyOfficer;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Member;
use App\Services\DocumentStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentStorageService $storageService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user()->load('companies');

        $documents = Document::with(['company', 'member', 'category', 'uploader'])
            ->forUser($user)
            ->when($request->input('company_id'), fn ($q, $v) => $q->byCompany($v))
            ->when($request->input('member_id'), fn ($q, $v) => $q->byMember($v))
            ->when($request->input('category_id'), fn ($q, $v) => $q->byCategory($v))
            ->when($request->input('status') === 'expiring', fn ($q) => $q->expiring())
            ->when($request->input('status') === 'expired', fn ($q) => $q->expired())
            ->when($request->input('status') === 'valid', fn ($q) => $q->valid())
            ->when($request->input('search'), function ($q, $search) {
                $q->where('title', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $companies  = Company::active()->forUser($user)->orderBy('denominazione')->get();
        $members    = Member::active()->forUser($user)->orderByName()->get();
        $categories = DocumentCategory::orderBy('sort_order')->get();

        return view('documents.index', compact('documents', 'companies', 'members', 'categories'));
    }

    public function create(Request $request): View
    {
        $user = $request->user()->load('companies');

        $companies         = Company::active()->forUser($user)->orderBy('denominazione')->get();
        $members           = Member::active()->forUser($user)->orderByName()->get();
        $companyCategories = DocumentCategory::forCompany()->orderBy('sort_order')->get();
        $memberCategories  = DocumentCategory::forMember()->orderBy('sort_order')->get();
        $preselectedMemberId = $request->input('member_id');

        return view('documents.upload', compact('companies', 'members', 'companyCategories', 'memberCategories', 'preselectedMemberId'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $user      = $request->user()->load('companies');
        $file      = $request->file('file');
        $category  = DocumentCategory::findOrFail($request->input('document_category_id'));
        $memberId  = $request->input('member_id');
        $companyId = $request->input('company_id');

        if ($companyId) {
            abort_unless($user->canAccessCompany((int) $companyId), 403);
        }

        if ($memberId) {
            $path = $this->storageService->storeForMember($file, $memberId, $category->name);
        } else {
            $path = $this->storageService->store($file, $companyId, $category->name);
        }

        $document = Document::create([
            'company_id'           => $companyId,
            'member_id'            => $memberId,
            'document_category_id' => $request->input('document_category_id'),
            'title'                => $request->input('title'),
            'description'          => $request->input('description'),
            'file_path'            => $path,
            'file_name_original'   => $file->getClientOriginalName(),
            'file_mime_type'       => $file->getMimeType(),
            'file_size'            => $file->getSize(),
            'expiration_date'      => $request->input('expiration_date'),
            'uploaded_by'          => $user->id,
        ]);

        $this->logActivity($request, 'uploaded', "Caricato documento: {$document->title}", Document::class, $document->id);

        return redirect()->route('documents.index')
            ->with('success', 'Documento caricato con successo.');
    }

    public function show(Request $request, Document $document): View
    {
        $user = $request->user()->load('companies');

        if ($document->company_id) {
            abort_unless($user->canAccessCompany($document->company_id), 403);
        }

        $document->load(['company', 'member', 'category', 'uploader', 'versions.uploader']);

        return view('documents.show', compact('document'));
    }

    public function download(Request $request, Document $document): StreamedResponse
    {
        if (!$request->user()->hasPermission('documents.download')) {
            abort(403);
        }

        $user = $request->user()->load('companies');

        if ($document->company_id) {
            abort_unless($user->canAccessCompany($document->company_id), 403);
        }

        $this->logActivity($request, 'downloaded', "Scaricato documento: {$document->title}", Document::class, $document->id);

        return $this->storageService->download($document);
    }

    public function uploadNewVersion(Request $request, Document $document): RedirectResponse
    {
        $user = $request->user()->load('companies');

        if ($document->company_id) {
            abort_unless($user->canAccessCompany($document->company_id), 403);
        }

        $maxSize      = config('archivio.upload.max_size_mb', 50) * 1024;
        $allowedTypes = implode(',', config('archivio.upload.allowed_types', []));

        $request->validate([
            'file'         => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
            'change_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->storageService->storeVersion(
            $document,
            $request->file('file'),
            $request->user(),
            $request->input('change_notes')
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Nuova versione caricata con successo.');
    }

    public function versions(Document $document): View
    {
        $document->load(['versions.uploader']);

        return view('documents.versions', compact('document'));
    }

    public function expiring(Request $request): View
    {
        $user = $request->user()->load('companies');
        $companyIds = $user->accessibleCompanyIds(); // null = admin, [] = nessuna, [1,2] = filtrato

        // Documenti
        $expiring = Document::withDetails()
            ->forUser($user)
            ->expiring()
            ->orderBy('expiration_date')
            ->get();

        $expired = Document::withDetails()
            ->forUser($user)
            ->expired()
            ->orderBy('expiration_date')
            ->get();

        // Cariche societarie (non cessate, con data_scadenza impostata)
        $officersBase = CompanyOfficer::with(['company', 'member'])
            ->whereNull('data_cessazione')
            ->whereNotNull('data_scadenza')
            ->when($companyIds !== null, fn ($q) => $q->whereIn('company_id', $companyIds));

        $expiringOfficers = (clone $officersBase)
            ->where('data_scadenza', '>=', now())
            ->where('data_scadenza', '<=', now()->addDays(90))
            ->orderBy('data_scadenza')
            ->get();

        $expiredOfficers = (clone $officersBase)
            ->where('data_scadenza', '<', now())
            ->orderByDesc('data_scadenza')
            ->get();

        // White list membri
        $wlBase = Member::where('white_list', true)
            ->whereNotNull('white_list_scadenza')
            ->when($companyIds !== null, fn ($q) => $q->whereHas(
                'officers', fn ($q2) => $q2->whereIn('company_id', $companyIds)
            ));

        $expiringWhiteList = (clone $wlBase)
            ->where('white_list_scadenza', '>=', now())
            ->where('white_list_scadenza', '<=', now()->addDays(90))
            ->orderBy('white_list_scadenza')
            ->get();

        $expiredWhiteList = (clone $wlBase)
            ->where('white_list_scadenza', '<', now())
            ->orderByDesc('white_list_scadenza')
            ->get();

        return view('documents.expiring', compact(
            'expiring', 'expired',
            'expiringOfficers', 'expiredOfficers',
            'expiringWhiteList', 'expiredWhiteList'
        ));
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        if (!$request->user()->hasPermission('documents.delete')) {
            abort(403);
        }

        $user = $request->user()->load('companies');

        if ($document->company_id) {
            abort_unless($user->canAccessCompany($document->company_id), 403);
        }

        $this->storageService->delete($document);
        $title = $document->title;
        $document->delete();

        $this->logActivity($request, 'deleted', "Eliminato documento: {$title}", Document::class, $document->id);

        return redirect()->route('documents.index')
            ->with('success', 'Documento eliminato.');
    }
}
