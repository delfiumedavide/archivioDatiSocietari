<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RegistroContabile;
use App\Models\RegistroContabileVersion;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class RegistroContabileController extends Controller
{
    public function __construct(private StorageService $storage) {}
    public function index(Request $request): View
    {
        $user = $request->user()->load('companies');

        // Anno corrente come default se non specificato
        $annoFilter = $request->input('anno', (string) now()->year);

        $registri = RegistroContabile::with(['company', 'uploader'])
            ->forUser($user)
            ->when($request->input('company_id'), fn ($q, $v) => $q->byCompany((int) $v))
            ->when($annoFilter, fn ($q, $v) => $q->byAnno((int) $v))
            ->when($request->input('tipo'), fn ($q, $v) => $q->byTipo($v))
            ->when($request->input('mese'), fn ($q, $v) => $q->byMese((int) $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $companies = Company::active()->forUser($user)->orderBy('denominazione')->get();
        $anni      = $this->anniDisponibili();
        $tipi      = RegistroContabile::TIPI;
        $mesi      = RegistroContabile::MESI;

        return view('registri-contabili.index', compact('registri', 'companies', 'anni', 'tipi', 'mesi', 'annoFilter'));
    }

    public function create(Request $request): View
    {
        $user        = $request->user()->load('companies');
        $companies   = Company::active()->forUser($user)->orderBy('denominazione')->get();
        $anni        = $this->anniDisponibili();
        $tipi        = RegistroContabile::TIPI;
        $tipiMensili = RegistroContabile::TIPI_IVA_MENSILI;
        $mesi        = RegistroContabile::MESI;

        return view('registri-contabili.create', compact('companies', 'anni', 'tipi', 'tipiMensili', 'mesi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user()->load('companies');

        $maxSize      = config('archivio.upload.max_size_mb', 50) * 1024;
        $allowedTypes = implode(',', config('archivio.upload.allowed_types', []));

        $isMensile = in_array($request->input('tipo'), RegistroContabile::TIPI_IVA_MENSILI, true);

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'anno'       => ['required', 'integer', 'min:2000', 'max:' . (now()->year + 1)],
            'tipo'       => ['required', 'string', 'in:' . implode(',', array_keys(RegistroContabile::TIPI))],
            'mese'       => [$isMensile ? 'required' : 'nullable', 'nullable', 'integer', 'min:1', 'max:12'],
            'titolo'     => ['required', 'string', 'max:255'],
            'note'       => ['nullable', 'string', 'max:5000'],
            'file'       => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
        ], [
            'company_id.required' => 'Selezionare una societa.',
            'anno.required'       => "L'anno e obbligatorio.",
            'tipo.required'       => 'Selezionare il tipo di registro.',
            'mese.required'       => 'Selezionare il mese per i registri IVA mensili.',
            'titolo.required'     => 'Il titolo e obbligatorio.',
            'file.required'       => 'Selezionare un file da caricare.',
            'file.max'            => "Il file supera la dimensione massima di {$maxSize} KB.",
            'file.mimes'          => 'Formato file non consentito.',
        ]);

        abort_unless($user->canAccessCompany((int) $validated['company_id']), 403);

        $file = $request->file('file');
        $ext  = $file->getClientOriginalExtension();
        $hash = hash('sha256', $file->getClientOriginalName() . time() . Str::random(16));
        $path = "registri-contabili/{$validated['company_id']}/{$validated['anno']}/{$hash}.{$ext}";

        $this->storage->putFileAs(
            "registri-contabili/{$validated['company_id']}/{$validated['anno']}",
            $file,
            "{$hash}.{$ext}"
        );

        $registro = RegistroContabile::create([
            'company_id'         => $validated['company_id'],
            'anno'               => $validated['anno'],
            'tipo'               => $validated['tipo'],
            'mese'               => $validated['mese'] ?? null,
            'titolo'             => $validated['titolo'],
            'note'               => $validated['note'] ?? null,
            'file_path'          => $path,
            'file_name_original' => $file->getClientOriginalName(),
            'file_mime_type'     => $file->getMimeType(),
            'file_size'          => $file->getSize(),
            'uploaded_by'        => $user->id,
        ]);

        $this->logActivity(
            $request,
            'uploaded',
            "Caricato libro/registro: {$registro->titolo} ({$registro->anno})",
            RegistroContabile::class,
            $registro->id
        );

        return redirect()->route('registri-contabili.index')
            ->with('success', 'Registro caricato con successo.');
    }

    public function show(Request $request, RegistroContabile $registro): View
    {
        $user = $request->user()->load('companies');

        abort_unless($user->canAccessCompany($registro->company_id), 403);

        $registro->load(['company', 'uploader', 'versions.uploader']);

        return view('registri-contabili.show', compact('registro'));
    }

    public function download(Request $request, RegistroContabile $registro): StreamedResponse
    {
        if (!$request->user()->hasPermission('registri_contabili.download')) {
            abort(403);
        }

        $user = $request->user()->load('companies');

        abort_unless($user->canAccessCompany($registro->company_id), 403);

        if (!$this->storage->exists($registro->file_path)) {
            abort(404, 'File non trovato.');
        }

        $this->logActivity(
            $request,
            'downloaded',
            "Scaricato libro/registro: {$registro->titolo} ({$registro->anno})",
            RegistroContabile::class,
            $registro->id
        );

        return $this->storage->download(
            $registro->file_path,
            $registro->file_name_original
        );
    }

    public function downloadVersion(Request $request, RegistroContabile $registro, RegistroContabileVersion $version): StreamedResponse
    {
        if (!$request->user()->hasPermission('registri_contabili.download')) {
            abort(403);
        }

        abort_unless($version->registro_id === $registro->id, 404);

        $user = $request->user()->load('companies');

        abort_unless($user->canAccessCompany($registro->company_id), 403);

        if (!$this->storage->exists($version->file_path)) {
            abort(404, 'File versione non trovato.');
        }

        $ext          = pathinfo($registro->file_name_original, PATHINFO_EXTENSION);
        $baseName     = pathinfo($registro->file_name_original, PATHINFO_FILENAME);
        $downloadName = "{$baseName}_v{$version->version}.{$ext}";

        $this->logActivity(
            $request,
            'downloaded',
            "Scaricata v{$version->version} di: {$registro->titolo}",
            RegistroContabile::class,
            $registro->id
        );

        return $this->storage->download($version->file_path, $downloadName);
    }

    public function uploadNewVersion(Request $request, RegistroContabile $registro): RedirectResponse
    {
        $user = $request->user()->load('companies');

        abort_unless($user->canAccessCompany($registro->company_id), 403);

        $maxSize      = config('archivio.upload.max_size_mb', 50) * 1024;
        $allowedTypes = implode(',', config('archivio.upload.allowed_types', []));

        $request->validate([
            'file'         => ['required', 'file', "max:{$maxSize}", "mimes:{$allowedTypes}"],
            'change_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Salva la versione corrente nello storico
        RegistroContabileVersion::create([
            'registro_id'    => $registro->id,
            'version'        => $registro->current_version,
            'file_path'      => $registro->file_path,
            'file_size'      => $registro->file_size,
            'file_mime_type' => $registro->file_mime_type,
            'uploaded_by'    => $registro->uploaded_by,
            'change_notes'   => $request->input('change_notes'),
            'created_at'     => $registro->updated_at ?? $registro->created_at,
        ]);

        $file  = $request->file('file');
        $ext   = $file->getClientOriginalExtension();
        $hash  = hash('sha256', $file->getClientOriginalName() . time() . Str::random(16));
        $path  = "registri-contabili/{$registro->company_id}/{$registro->anno}/{$hash}.{$ext}";

        $this->storage->putFileAs(
            "registri-contabili/{$registro->company_id}/{$registro->anno}",
            $file,
            "{$hash}.{$ext}"
        );

        $registro->update([
            'file_path'          => $path,
            'file_name_original' => $file->getClientOriginalName(),
            'file_mime_type'     => $file->getMimeType(),
            'file_size'          => $file->getSize(),
            'current_version'    => $registro->current_version + 1,
            'uploaded_by'        => $user->id,
        ]);

        $this->logActivity(
            $request,
            'uploaded',
            "Nuova versione libro/registro: {$registro->titolo} (v{$registro->current_version})",
            RegistroContabile::class,
            $registro->id
        );

        return redirect()->route('registri-contabili.show', $registro)
            ->with('success', 'Nuova versione caricata con successo.');
    }

    public function exportZip(Request $request)
    {
        if (!$request->user()->hasPermission('registri_contabili.download')) {
            abort(403);
        }

        $user = $request->user()->load('companies');
        $ids  = array_filter(explode(',', $request->input('ids', '')), 'is_numeric');

        abort_if(empty($ids), 422, 'Nessun registro selezionato.');

        $registri = RegistroContabile::with('company')
            ->whereIn('id', $ids)
            ->forUser($user)
            ->get();

        abort_if($registri->isEmpty(), 404, 'Nessun registro trovato.');

        $tmpPath = sys_get_temp_dir() . '/registri_' . Str::random(12) . '.zip';

        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($registri as $registro) {
            if (!$this->storage->exists($registro->file_path)) {
                continue;
            }

            $ext     = pathinfo($registro->file_name_original, PATHINFO_EXTENSION);
            $zipName = Str::slug($registro->company->denominazione ?? 'societa')
                . '_' . $registro->anno
                . '_' . Str::slug($registro->tipo_label)
                . '.' . $ext;

            $zip->addFromString($zipName, $this->storage->get($registro->file_path));
        }

        $zip->close();

        $this->logActivity(
            $request,
            'downloaded',
            'Export ZIP libri/registri (' . $registri->count() . ' file)',
            RegistroContabile::class,
            0
        );

        $fileName = 'registri_contabili_' . now()->format('Ymd_His') . '.zip';

        return response()->download($tmpPath, $fileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function completezza(Request $request): View
    {
        $user      = $request->user()->load('companies');
        $anno      = (int) $request->input('anno', now()->year);
        $companies = Company::active()->forUser($user)->orderBy('denominazione')->get();
        $anni      = $this->anniDisponibili();

        $companyIds = $companies->pluck('id')->all();

        // Tipi annuali (non IVA mensili)
        $tipiAnnuali = RegistroContabile::tipiAnnuali();

        // Registri annuali presenti: company_id → tipo → registro
        $presentiAnnuali = RegistroContabile::whereIn('company_id', $companyIds)
            ->where('anno', $anno)
            ->whereNull('mese')
            ->get(['id', 'company_id', 'tipo'])
            ->groupBy('company_id')
            ->map(fn ($items) => $items->keyBy('tipo'));

        // Mesi da verificare: 1-12 per anni passati, 1-meseCorrente per anno corrente
        $meseCorrente   = now()->year === $anno ? now()->month : 12;
        $mesiDaVerificare = range(1, $meseCorrente);
        $mesiLabels     = RegistroContabile::MESI;

        // Tipi IVA mensili (standard, senza margine)
        $tipiMensiliStd = RegistroContabile::tipiMensiliStandard();

        // Tipi IVA margine: slug => label (sottoinsieme di TIPI)
        $tipiMensiliMargine = array_intersect_key(RegistroContabile::TIPI, array_flip(RegistroContabile::TIPI_IVA_MARGINE));

        // Registri mensili presenti: company_id → mese → tipo → registro (con id)
        $presentiMensili = RegistroContabile::whereIn('company_id', $companyIds)
            ->where('anno', $anno)
            ->whereNotNull('mese')
            ->get(['id', 'company_id', 'tipo', 'mese'])
            ->groupBy('company_id')
            ->map(fn ($byCompany) =>
                $byCompany->groupBy('mese')
                    ->map(fn ($byMese) => $byMese->keyBy('tipo'))  // tipo => registro (con id)
            );

        $tipiMensiliShort = RegistroContabile::TIPI_IVA_MENSILI_SHORT;

        return view('registri-contabili.completezza', compact(
            'companies', 'tipiAnnuali', 'anni', 'anno',
            'presentiAnnuali', 'mesiDaVerificare', 'mesiLabels',
            'tipiMensiliStd', 'tipiMensiliMargine', 'tipiMensiliShort', 'presentiMensili'
        ));
    }

    public function destroy(Request $request, RegistroContabile $registro): RedirectResponse
    {
        if (!$request->user()->hasPermission('registri_contabili.delete')) {
            abort(403);
        }

        $user = $request->user()->load('companies');

        abort_unless($user->canAccessCompany($registro->company_id), 403);

        $titolo = $registro->titolo;
        $registro->delete();

        $this->logActivity(
            $request,
            'deleted',
            "Eliminato libro/registro: {$titolo}",
            RegistroContabile::class,
            $registro->id
        );

        return redirect()->route('registri-contabili.index')
            ->with('success', 'Registro eliminato.');
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function anniDisponibili(): array
    {
        $current = now()->year;
        $anni    = [];

        for ($y = $current; $y >= $current - 14; $y--) {
            $anni[] = $y;
        }

        return $anni;
    }
}
