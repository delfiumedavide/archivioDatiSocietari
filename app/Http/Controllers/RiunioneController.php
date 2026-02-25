<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Member;
use App\Models\Riunione;
use App\Models\RiunionePartecipante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RiunioneController extends Controller
{
    // ─── Index / Dashboard ──────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $upcoming = Riunione::with('company')
            ->upcoming()
            ->orderBy('data_ora')
            ->limit(5)
            ->get();

        $missingVerbale = Riunione::with('company')
            ->missingVerbale()
            ->orderBy('data_ora', 'desc')
            ->get();

        $upcomingCount      = Riunione::upcoming()->count();
        $missingVerbaleCount = Riunione::missingVerbale()->count();
        $yearCount          = Riunione::whereYear('data_ora', now()->year)->count();

        // Full filtered list
        $query = Riunione::with('company')->orderBy('data_ora', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('anno')) {
            $query->whereYear('data_ora', $request->anno);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riunioni  = $query->paginate(20)->withQueryString();
        $companies = Company::active()->forUser(auth()->user()->load('companies'))->orderBy('denominazione')->get();

        $anni = Riunione::selectRaw('YEAR(data_ora) as anno')
            ->groupBy('anno')
            ->orderByDesc('anno')
            ->pluck('anno');

        return view('libri-sociali.index', compact(
            'upcoming',
            'missingVerbale',
            'upcomingCount',
            'missingVerbaleCount',
            'yearCount',
            'riunioni',
            'companies',
            'anni',
        ));
    }

    // ─── Create / Store ─────────────────────────────────────────────────────────

    public function create(): View
    {
        $companies = Company::active()->forUser(auth()->user()->load('companies'))->orderBy('denominazione')->get();
        return view('libri-sociali.create', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id'        => 'required|exists:companies,id',
            'tipo'              => 'required|in:cda,collegio_sindacale,assemblea_ordinaria,assemblea_straordinaria',
            'data_ora'          => 'required|date',
            'luogo'             => 'nullable|string|max:255',
            'ordine_del_giorno' => 'nullable|string|max:10000',
            'note'              => 'nullable|string|max:5000',
        ]);

        $riunione = Riunione::create([
            ...$validated,
            'status'     => 'programmata',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', 'Riunione creata con successo.');
    }

    // ─── Show ───────────────────────────────────────────────────────────────────

    public function show(Riunione $riunione): View
    {
        $riunione->load([
            'company',
            'delibere',
            'partecipanti.member',
            'creator',
        ]);

        // Members of the company for the partecipanti form
        $companyMembers = Member::whereHas('officers', fn ($q) => $q->where('company_id', $riunione->company_id))
            ->orderByName()
            ->get();

        // Build a keyed map of current partecipanti for the form
        $partecipantiMap = $riunione->partecipanti->keyBy('member_id');

        return view('libri-sociali.show', compact('riunione', 'companyMembers', 'partecipantiMap'));
    }

    // ─── Edit / Update ──────────────────────────────────────────────────────────

    public function edit(Riunione $riunione): View
    {
        $companies = Company::active()->forUser(auth()->user()->load('companies'))->orderBy('denominazione')->get();
        return view('libri-sociali.edit', compact('riunione', 'companies'));
    }

    public function update(Request $request, Riunione $riunione): RedirectResponse
    {
        $validated = $request->validate([
            'company_id'        => 'required|exists:companies,id',
            'tipo'              => 'required|in:cda,collegio_sindacale,assemblea_ordinaria,assemblea_straordinaria',
            'data_ora'          => 'required|date',
            'luogo'             => 'nullable|string|max:255',
            'ordine_del_giorno' => 'nullable|string|max:10000',
            'note'              => 'nullable|string|max:5000',
        ]);

        $riunione->update($validated);

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', 'Riunione aggiornata.');
    }

    // ─── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(Riunione $riunione): RedirectResponse
    {
        // Remove stored files
        if ($riunione->convocazione_path) {
            Storage::disk('documents')->delete($riunione->convocazione_path);
        }
        if ($riunione->verbale_path) {
            Storage::disk('documents')->delete($riunione->verbale_path);
        }

        $riunione->delete();

        return redirect()->route('libri-sociali.index')
            ->with('success', 'Riunione eliminata.');
    }

    // ─── Status Advancement ─────────────────────────────────────────────────────

    public function advanceStatus(Request $request, Riunione $riunione): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:programmata,convocata,svolta,annullata',
        ]);

        $riunione->update(['status' => $validated['status']]);

        $label = $riunione->fresh()->status_label;

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', "Stato aggiornato: {$label}.");
    }

    // ─── File Upload / Download ─────────────────────────────────────────────────

    public function uploadConvocazione(Request $request, Riunione $riunione): RedirectResponse
    {
        $request->validate([
            'convocazione' => 'required|file|mimes:pdf|max:20480',
        ]);

        if ($riunione->convocazione_path) {
            Storage::disk('documents')->delete($riunione->convocazione_path);
        }

        $path = $request->file('convocazione')->storeAs(
            "libri-sociali/{$riunione->id}",
            'convocazione.pdf',
            'documents'
        );

        $riunione->update(['convocazione_path' => $path]);

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', 'Convocazione caricata.');
    }

    public function uploadVerbale(Request $request, Riunione $riunione): RedirectResponse
    {
        $request->validate([
            'verbale' => 'required|file|mimes:pdf|max:20480',
        ]);

        if ($riunione->verbale_path) {
            Storage::disk('documents')->delete($riunione->verbale_path);
        }

        $path = $request->file('verbale')->storeAs(
            "libri-sociali/{$riunione->id}",
            'verbale.pdf',
            'documents'
        );

        $riunione->update(['verbale_path' => $path]);

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', 'Verbale caricato.');
    }

    public function downloadConvocazione(Riunione $riunione): StreamedResponse
    {
        abort_unless($riunione->convocazione_path, 404);
        abort_unless(Storage::disk('documents')->exists($riunione->convocazione_path), 404);

        $filename = "Convocazione_{$riunione->tipo_short}_{$riunione->data_ora->format('Y-m-d')}.pdf";

        return Storage::disk('documents')->download($riunione->convocazione_path, $filename);
    }

    public function downloadVerbale(Riunione $riunione): StreamedResponse
    {
        abort_unless($riunione->verbale_path, 404);
        abort_unless(Storage::disk('documents')->exists($riunione->verbale_path), 404);

        $filename = "Verbale_{$riunione->tipo_short}_{$riunione->data_ora->format('Y-m-d')}.pdf";

        return Storage::disk('documents')->download($riunione->verbale_path, $filename);
    }

    // ─── Partecipanti ───────────────────────────────────────────────────────────

    public function storePartecipanti(Request $request, Riunione $riunione): RedirectResponse
    {
        $validated = $request->validate([
            'partecipanti'             => 'nullable|array',
            'partecipanti.*.member_id' => 'required|exists:members,id',
            'partecipanti.*.presenza'  => 'required|in:presente,assente,delegato',
            'partecipanti.*.note'      => 'nullable|string|max:255',
        ]);

        // Delete existing and re-insert (simple sync)
        $riunione->partecipanti()->delete();

        foreach ($validated['partecipanti'] ?? [] as $item) {
            RiunionePartecipante::create([
                'riunione_id' => $riunione->id,
                'member_id'   => $item['member_id'],
                'presenza'    => $item['presenza'],
                'note'        => $item['note'] ?? null,
            ]);
        }

        return redirect()->route('libri-sociali.show', $riunione)
            ->with('success', 'Presenti aggiornati.');
    }
}
