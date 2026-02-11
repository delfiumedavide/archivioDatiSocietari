<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        return view('dashboard.index', [
            'stats' => $this->dashboardService->getSummaryStats(),
            'expirationData' => $this->dashboardService->getExpirationData(),
            'documentsByCategory' => $this->dashboardService->getDocumentsByCategory(),
            'documentsByCompany' => $this->dashboardService->getDocumentsByCompany(),
            'uploadActivity' => $this->dashboardService->getUploadActivity(),
            'recentActivity' => $this->dashboardService->getRecentActivity(),
            'expiringDocuments' => $this->dashboardService->getExpiringDocumentsList(),
        ]);
    }
}
