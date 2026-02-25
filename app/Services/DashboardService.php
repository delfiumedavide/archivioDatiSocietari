<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\FamilyStatusDeclaration;
use App\Models\Member;
use App\Models\Riunione;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Applica filtro company_id alla query.
     * null = admin (no filtro), [] = nessun accesso, [1,2] = solo quelle
     */
    private function whenCompanies($query, ?array $ids, string $col = 'company_id')
    {
        if ($ids === null) return $query;
        return empty($ids) ? $query->whereRaw('0 = 1') : $query->whereIn($col, $ids);
    }

    private function whenMembersOfCompanies($query, ?array $ids)
    {
        if ($ids === null) return $query;
        return empty($ids)
            ? $query->whereRaw('0 = 1')
            : $query->whereHas('officers', fn ($q) => $q->whereIn('company_id', $ids));
    }

    /**
     * Scope documento per company (con fallback per documenti membro senza company_id)
     */
    private function applyDocumentScope($query, ?array $ids)
    {
        if ($ids === null) return $query;
        if (empty($ids))   return $query->whereRaw('0 = 1');

        return $query->where(function ($q) use ($ids) {
            $q->whereIn('company_id', $ids)
              ->orWhere(function ($q2) use ($ids) {
                  $q2->whereNull('company_id')
                     ->whereHas('member.officers', fn ($q3) => $q3->whereIn('company_id', $ids));
              });
        });
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function ck(string $method, ?array $ids, string $extra = ''): string
    {
        return 'dashboard.' . $method . '.' . md5(json_encode($ids) . $extra);
    }

    // ─── Stats ─────────────────────────────────────────────────────────────────

    public function getSummaryStats(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('summary', $companyIds), 300, function () use ($companyIds) {
            return [
                'total_companies' => $this->whenCompanies(Company::active(), $companyIds, 'id')->count(),
                'total_members'   => $this->whenMembersOfCompanies(Member::active(), $companyIds)->count(),
                'total_documents' => $this->applyDocumentScope(Document::query(), $companyIds)->count(),
                'expiring_count'  => $this->applyDocumentScope(Document::query(), $companyIds)->expiring()->count(),
                'expired_count'   => $this->applyDocumentScope(Document::query(), $companyIds)->expired()->count(),
            ];
        });
    }

    public function getRiunioniStats(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('riunioni', $companyIds), 300, function () use ($companyIds) {
            return [
                'upcoming'        => $this->whenCompanies(Riunione::upcoming(), $companyIds)->count(),
                'missing_verbale' => $this->whenCompanies(Riunione::missingVerbale(), $companyIds)->count(),
                'year'            => $this->whenCompanies(Riunione::whereYear('data_ora', now()->year), $companyIds)->count(),
            ];
        });
    }

    public function getProssimeRiunioni(?array $companyIds = null, int $limit = 5): Collection
    {
        return $this->whenCompanies(Riunione::with('company')->upcoming(), $companyIds)
            ->orderBy('data_ora')
            ->limit($limit)
            ->get();
    }

    public function getMissingVerbali(?array $companyIds = null, int $limit = 5): Collection
    {
        return $this->whenCompanies(Riunione::with('company')->missingVerbale(), $companyIds)
            ->orderByDesc('data_ora')
            ->limit($limit)
            ->get();
    }

    public function getDeclarationStats(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('declarations', $companyIds), 300, function () use ($companyIds) {
            $year = now()->year;

            $base = FamilyStatusDeclaration::forYear($year);

            if ($companyIds !== null) {
                if (empty($companyIds)) {
                    $base = $base->whereRaw('0 = 1');
                } else {
                    $ids = $companyIds;
                    $base = $base->whereHas('member.officers', fn ($q) => $q->whereIn('company_id', $ids));
                }
            }

            $generated = (clone $base)->count();
            $signed    = (clone $base)->signed()->count();

            return [
                'generated' => $generated,
                'unsigned'  => $generated - $signed,
                'signed'    => $signed,
            ];
        });
    }

    // ─── Charts ────────────────────────────────────────────────────────────────

    public function getExpirationData(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('expiration', $companyIds), 300, function () use ($companyIds) {
            $base = $this->applyDocumentScope(Document::query(), $companyIds);

            return [
                'labels' => ['Validi', 'In Scadenza', 'Scaduti'],
                'data'   => [
                    (clone $base)->valid()->count(),
                    (clone $base)->expiring()->count(),
                    (clone $base)->expired()->count(),
                ],
                'colors' => ['#10b981', '#f59e0b', '#ef4444'],
            ];
        });
    }

    public function getDocumentsByCategory(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('docs_by_category', $companyIds), 300, function () use ($companyIds) {
            $categories = DocumentCategory::withCount(['documents' => function ($q) use ($companyIds) {
                $this->applyDocumentScope($q, $companyIds);
            }])->orderBy('sort_order')->get();

            return [
                'labels' => $categories->pluck('label')->toArray(),
                'data'   => $categories->pluck('documents_count')->toArray(),
                'colors' => [
                    '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b',
                    '#10b981', '#06b6d4', '#6366f1', '#f97316',
                    '#84cc16', '#64748b',
                ],
            ];
        });
    }

    public function getDocumentsByCompany(?array $companyIds = null): array
    {
        return Cache::remember($this->ck('docs_by_company', $companyIds), 300, function () use ($companyIds) {
            $companies = $this->whenCompanies(Company::active(), $companyIds, 'id')
                ->withCount('documents')
                ->orderByDesc('documents_count')
                ->get();

            return [
                'labels' => $companies->pluck('denominazione')->toArray(),
                'data'   => $companies->pluck('documents_count')->toArray(),
            ];
        });
    }

    public function getUploadActivity(int $months = 12, ?array $companyIds = null): array
    {
        return Cache::remember($this->ck('upload_activity', $companyIds, (string) $months), 300, function () use ($months, $companyIds) {
            $data = $this->applyDocumentScope(Document::query(), $companyIds)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $labels = [];
            $values = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $date     = now()->subMonths($i);
                $key      = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $values[] = $data->firstWhere('month', $key)?->total ?? 0;
            }

            return [
                'labels' => $labels,
                'data'   => $values,
            ];
        });
    }

    public function getRecentActivity(int $limit = 10): Collection
    {
        return ActivityLog::with('user')
            ->recent($limit)
            ->get();
    }

    public function getExpiringDocumentsList(int $limit = 20, ?array $companyIds = null): Collection
    {
        return $this->applyDocumentScope(
            Document::withDetails(),
            $companyIds
        )
        ->where(function ($q) {
            $q->expired()->orWhere(fn ($q2) => $q2->expiring());
        })
        ->orderByRaw("CASE WHEN expiration_date < NOW() THEN 0 ELSE 1 END, expiration_date ASC")
        ->limit($limit)
        ->get();
    }
}
