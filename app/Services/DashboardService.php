<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryStats(): array
    {
        return [
            'total_companies' => Company::active()->count(),
            'total_documents' => Document::count(),
            'expiring_count' => Document::expiring()->count(),
            'expired_count' => Document::expired()->count(),
        ];
    }

    public function getExpirationData(): array
    {
        $valid = Document::valid()->count();
        $expiring = Document::expiring()->count();
        $expired = Document::expired()->count();

        return [
            'labels' => ['Validi', 'In Scadenza', 'Scaduti'],
            'data' => [$valid, $expiring, $expired],
            'colors' => ['#10b981', '#f59e0b', '#ef4444'],
        ];
    }

    public function getDocumentsByCategory(): array
    {
        $categories = DocumentCategory::withCount('documents')
            ->orderBy('sort_order')
            ->get();

        return [
            'labels' => $categories->pluck('label')->toArray(),
            'data' => $categories->pluck('documents_count')->toArray(),
            'colors' => [
                '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b',
                '#10b981', '#06b6d4', '#6366f1', '#f97316',
                '#84cc16', '#64748b',
            ],
        ];
    }

    public function getDocumentsByCompany(): array
    {
        $companies = Company::active()
            ->withCount('documents')
            ->orderByDesc('documents_count')
            ->get();

        return [
            'labels' => $companies->pluck('denominazione')->toArray(),
            'data' => $companies->pluck('documents_count')->toArray(),
        ];
    }

    public function getUploadActivity(int $months = 12): array
    {
        $data = Document::select(
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
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y');
            $values[] = $data->firstWhere('month', $key)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    public function getRecentActivity(int $limit = 10): Collection
    {
        return ActivityLog::with('user')
            ->recent($limit)
            ->get();
    }

    public function getExpiringDocumentsList(int $limit = 20): Collection
    {
        return Document::with(['company', 'category'])
            ->where(function ($q) {
                $q->expired()->orWhere(fn ($q2) => $q2->expiring());
            })
            ->orderByRaw("CASE WHEN expiration_date < NOW() THEN 0 ELSE 1 END, expiration_date ASC")
            ->limit($limit)
            ->get();
    }
}
