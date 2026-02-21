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
        $user       = auth()->user()->load('companies');
        $companyIds = $user->accessibleCompanyIds(); // null = admin, array = restricted

        $data = [
            'stats'               => $this->dashboardService->getSummaryStats($companyIds),
            'expirationData'      => $this->dashboardService->getExpirationData($companyIds),
            'documentsByCategory' => $this->dashboardService->getDocumentsByCategory($companyIds),
            'documentsByCompany'  => $this->dashboardService->getDocumentsByCompany($companyIds),
            'uploadActivity'      => $this->dashboardService->getUploadActivity(12, $companyIds),
            'recentActivity'      => $this->dashboardService->getRecentActivity(10),
            'expiringDocuments'   => $this->dashboardService->getExpiringDocumentsList(20, $companyIds),
        ];

        if ($user->isAdmin()) {
            $data['riunioniStats']    = $this->dashboardService->getRiunioniStats();
            $data['prossimeRiunioni'] = $this->dashboardService->getProssimeRiunioni();
            $data['missingVerbali']   = $this->dashboardService->getMissingVerbali();
            $data['declarationStats'] = $this->dashboardService->getDeclarationStats();
        }

        return view('dashboard.index', $data);
    }
}
