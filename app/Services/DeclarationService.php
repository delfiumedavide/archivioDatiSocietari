<?php

namespace App\Services;

use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DeclarationService
{
    private string $disk = 'documents';

    public function generate(Member $member, int $year, int $userId): FamilyStatusDeclaration
    {
        $member->load(['familyMembers' => fn ($q) => $q->active()]);

        $statoCivile = $member->current_stato_civile;

        $pdf = Pdf::loadView('family-status.declaration-pdf', [
            'member' => $member,
            'anno' => $year,
            'statoCivile' => $statoCivile,
            'familyMembers' => $member->familyMembers->whereNull('data_fine'),
            'generatedAt' => now(),
        ]);

        $path = "declarations/{$member->id}/{$year}/dichiarazione.pdf";

        Storage::disk($this->disk)->put($path, $pdf->output());

        $declaration = FamilyStatusDeclaration::updateOrCreate(
            ['member_id' => $member->id, 'anno' => $year],
            [
                'stato_civile' => $statoCivile,
                'generated_path' => $path,
                'generated_at' => now(),
                'registered_by' => $userId,
            ]
        );

        return $declaration;
    }

    public function storeSigned(FamilyStatusDeclaration $declaration, UploadedFile $file): FamilyStatusDeclaration
    {
        $hash = Str::random(16);
        $extension = $file->getClientOriginalExtension();
        $path = "declarations/{$declaration->member_id}/{$declaration->anno}/firmata_{$hash}.{$extension}";

        Storage::disk($this->disk)->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        $declaration->update([
            'signed_path' => $path,
            'signed_at' => now(),
        ]);

        return $declaration;
    }

    public function downloadGenerated(FamilyStatusDeclaration $declaration): StreamedResponse
    {
        if (!$declaration->generated_path || !Storage::disk($this->disk)->exists($declaration->generated_path)) {
            abort(404, 'PDF non trovato.');
        }

        $member = $declaration->member;
        $filename = "dichiarazione_{$member->cognome}_{$member->nome}_{$declaration->anno}.pdf";

        return Storage::disk($this->disk)->download($declaration->generated_path, $filename);
    }

    public function downloadSigned(FamilyStatusDeclaration $declaration): StreamedResponse
    {
        if (!$declaration->signed_path || !Storage::disk($this->disk)->exists($declaration->signed_path)) {
            abort(404, 'PDF firmato non trovato.');
        }

        $member = $declaration->member;
        $ext = pathinfo($declaration->signed_path, PATHINFO_EXTENSION);
        $filename = "firmata_{$member->cognome}_{$member->nome}_{$declaration->anno}.{$ext}";

        return Storage::disk($this->disk)->download($declaration->signed_path, $filename);
    }

    public function buildBulkZip(int $year): string
    {
        $declarations = FamilyStatusDeclaration::with('member')
            ->forYear($year)
            ->whereNotNull('generated_path')
            ->get();

        $tempPath = storage_path("app/temp/dichiarazioni_{$year}.zip");
        $tempDir = dirname($tempPath);

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($declarations as $declaration) {
            if (Storage::disk($this->disk)->exists($declaration->generated_path)) {
                $member = $declaration->member;
                $zipName = "{$member->cognome}_{$member->nome}_{$declaration->anno}.pdf";
                $zip->addFromString($zipName, Storage::disk($this->disk)->get($declaration->generated_path));
            }
        }

        $zip->close();

        return $tempPath;
    }
}
