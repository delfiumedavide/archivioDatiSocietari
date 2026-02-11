<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentStorageService
{
    private string $disk = 'documents';

    public function store(UploadedFile $file, int $companyId, string $categorySlug): string
    {
        $hashedName = hash('sha256', $file->getClientOriginalName() . time() . Str::random(16));
        $extension = $file->getClientOriginalExtension();
        $year = date('Y');

        $path = "{$companyId}/{$categorySlug}/{$year}/{$hashedName}.{$extension}";

        Storage::disk($this->disk)->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        return $path;
    }

    public function storeVersion(Document $document, UploadedFile $file, User $user, ?string $changeNotes = null): DocumentVersion
    {
        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version' => $document->current_version,
            'file_path' => $document->file_path,
            'file_size' => $document->file_size,
            'file_mime_type' => $document->file_mime_type,
            'uploaded_by' => $document->uploaded_by,
            'change_notes' => $changeNotes,
            'created_at' => now(),
        ]);

        $newPath = $this->store($file, $document->company_id, $document->category->name);

        $document->update([
            'file_path' => $newPath,
            'file_name_original' => $file->getClientOriginalName(),
            'file_mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'current_version' => $document->current_version + 1,
            'uploaded_by' => $user->id,
            'expiration_notified' => false,
        ]);

        return $version;
    }

    public function download(Document $document): StreamedResponse
    {
        if (!Storage::disk($this->disk)->exists($document->file_path)) {
            abort(404, 'File non trovato.');
        }

        return Storage::disk($this->disk)->download(
            $document->file_path,
            $document->file_name_original
        );
    }

    public function downloadVersion(DocumentVersion $version, Document $document): StreamedResponse
    {
        if (!Storage::disk($this->disk)->exists($version->file_path)) {
            abort(404, 'File versione non trovato.');
        }

        $originalName = pathinfo($document->file_name_original, PATHINFO_FILENAME)
            . '_v' . $version->version
            . '.' . pathinfo($document->file_name_original, PATHINFO_EXTENSION);

        return Storage::disk($this->disk)->download($version->file_path, $originalName);
    }

    public function delete(Document $document): bool
    {
        foreach ($document->versions as $version) {
            Storage::disk($this->disk)->delete($version->file_path);
        }

        Storage::disk($this->disk)->delete($document->file_path);

        return true;
    }

    public function getAllowedMimeTypes(): array
    {
        return config('archivio.upload.allowed_mimes', []);
    }

    public function getMaxFileSize(): int
    {
        return config('archivio.upload.max_size_mb', 50) * 1024;
    }

    public function getAllowedExtensions(): array
    {
        return config('archivio.upload.allowed_types', []);
    }
}
