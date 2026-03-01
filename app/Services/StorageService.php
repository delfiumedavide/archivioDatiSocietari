<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageService
{
    public function __construct(private AppSettingsService $settingsService) {}

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Salva un file caricato nel path directory/name.
     * Ritorna il path completo relativo al disco.
     */
    public function putFileAs(string $directory, UploadedFile $file, string $name): string
    {
        $path = ltrim($directory . '/' . $name, '/');

        if ($this->isLocalActive()) {
            $this->localDisk()->putFileAs($directory, $file, $name);
        }

        if ($this->isExternalActive()) {
            $this->externalDisk()->putFileAs($directory, $file, $name);
        }

        return $path;
    }

    /**
     * Alias compatibile con $file->storeAs($dir, $name, 'documents').
     */
    public function storeAs(string $directory, string $name, UploadedFile $file): string
    {
        return $this->putFileAs($directory, $file, $name);
    }

    /**
     * Verifica se un file esiste nel disco attivo.
     */
    public function exists(string $path): bool
    {
        if ($this->mode() === 'external') {
            return $this->externalDisk()->exists($path);
        }

        if ($this->localDisk()->exists($path)) {
            return true;
        }

        if ($this->mode() === 'both') {
            return $this->externalDisk()->exists($path);
        }

        return false;
    }

    /**
     * Scarica un file come StreamedResponse.
     */
    public function download(string $path, string $fileName): StreamedResponse
    {
        return $this->readDisk($path)->download($path, $fileName);
    }

    /**
     * Legge e ritorna il contenuto di un file.
     */
    public function get(string $path): string
    {
        return $this->readDisk($path)->get($path) ?? '';
    }

    /**
     * Salva contenuto grezzo (stringa) in un path.
     * Usato per PDF generati al volo (non UploadedFile).
     */
    public function put(string $path, string $content): void
    {
        if ($this->isLocalActive()) {
            $this->localDisk()->put($path, $content);
        }

        if ($this->isExternalActive()) {
            $this->externalDisk()->put($path, $content);
        }
    }

    /**
     * Elimina un file da tutti i dischi attivi.
     */
    public function delete(string $path): void
    {
        if ($this->isLocalActive()) {
            $this->localDisk()->delete($path);
        }

        if ($this->isExternalActive()) {
            $this->externalDisk()->delete($path);
        }
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private function mode(): string
    {
        return $this->settingsService->get()->storage_mode ?? 'local';
    }

    private function isLocalActive(): bool
    {
        return $this->mode() !== 'external';
    }

    private function isExternalActive(): bool
    {
        $mode = $this->mode();

        if ($mode === 'local') {
            return false;
        }

        $path = $this->settingsService->get()->storage_external_path;

        return ! empty($path);
    }

    private function localDisk(): Filesystem
    {
        return Storage::disk('documents');
    }

    private function externalDisk(): Filesystem
    {
        return Storage::build([
            'driver' => 'local',
            'root'   => $this->settingsService->get()->storage_external_path,
        ]);
    }

    /**
     * Ritorna il disco da usare per le operazioni di lettura.
     * Se il file non è sul locale (mode=both), fallback sull'esterno.
     */
    private function readDisk(string $path): Filesystem
    {
        if ($this->mode() === 'external') {
            return $this->externalDisk();
        }

        if ($this->mode() === 'both' && ! $this->localDisk()->exists($path)) {
            return $this->externalDisk();
        }

        return $this->localDisk();
    }
}
