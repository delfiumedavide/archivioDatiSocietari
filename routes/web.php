<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyOfficerController;
use App\Http\Controllers\CompanyRelationshipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliberaController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FamilyStatusController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RiunioneController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Companies
    Route::middleware('permission:companies')->group(function () {
        Route::resource('companies', CompanyController::class);

        // Nested: Officers
        Route::post('companies/{company}/officers', [CompanyOfficerController::class, 'store'])->name('companies.officers.store');
        Route::put('officers/{officer}', [CompanyOfficerController::class, 'update'])->name('officers.update');
        Route::patch('officers/{officer}/cease', [CompanyOfficerController::class, 'cease'])->name('officers.cease');
        Route::delete('officers/{officer}', [CompanyOfficerController::class, 'destroy'])->name('officers.destroy');

        // Nested: Shareholders
        Route::post('companies/{company}/shareholders', [ShareholderController::class, 'store'])->name('companies.shareholders.store');
        Route::put('shareholders/{shareholder}', [ShareholderController::class, 'update'])->name('shareholders.update');
        Route::delete('shareholders/{shareholder}', [ShareholderController::class, 'destroy'])->name('shareholders.destroy');

        // Nested: Relationships
        Route::post('companies/{company}/relationships', [CompanyRelationshipController::class, 'store'])->name('companies.relationships.store');
        Route::delete('relationships/{relationship}', [CompanyRelationshipController::class, 'destroy'])->name('relationships.destroy');
    });

    // Members
    Route::middleware('permission:membri')->group(function () {
        Route::get('members/search', [MemberController::class, 'search'])->name('members.search');
        Route::resource('members', MemberController::class);
    });

    // Family Status
    Route::middleware('permission:stati_famiglia')->group(function () {
        Route::get('family-status', [FamilyStatusController::class, 'index'])->name('family-status.index');

        // Declarations (BEFORE {member} to avoid route conflicts)
        Route::get('family-status/declarations', [FamilyStatusController::class, 'declarations'])->name('family-status.declarations');
        Route::post('family-status/declarations/bulk-generate', [FamilyStatusController::class, 'bulkGenerate'])->name('family-status.declarations.bulk-generate');
        Route::get('family-status/declarations/bulk-download', [FamilyStatusController::class, 'bulkDownload'])->name('family-status.declarations.bulk-download');
        Route::get('family-status/declarations/{declaration}/download', [FamilyStatusController::class, 'downloadGenerated'])->name('family-status.declarations.download');
        Route::post('family-status/declarations/{declaration}/upload-signed', [FamilyStatusController::class, 'uploadSigned'])->name('family-status.declarations.upload-signed');
        Route::get('family-status/declarations/{declaration}/download-signed', [FamilyStatusController::class, 'downloadSigned'])->name('family-status.declarations.download-signed');
        Route::post('family-status/{member}/declarations/generate', [FamilyStatusController::class, 'generateDeclaration'])->name('family-status.declarations.generate');

        Route::get('family-status/{member}', [FamilyStatusController::class, 'show'])->name('family-status.show');
        Route::post('family-status/{member}/status-change', [FamilyStatusController::class, 'storeStatusChange'])->name('family-status.store-change');
        Route::post('family-status/{member}/family-member', [FamilyStatusController::class, 'storeFamilyMember'])->name('family-status.store-family-member');
        Route::put('family-members/{familyMember}', [FamilyStatusController::class, 'updateFamilyMember'])->name('family-members.update');
        Route::delete('family-members/{familyMember}', [FamilyStatusController::class, 'destroyFamilyMember'])->name('family-members.destroy');
    });

    // Documents
    Route::middleware('permission:documents')->group(function () {
        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/expiring', [DocumentController::class, 'expiring'])->name('documents.expiring');
        Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('documents/{document}/new-version', [DocumentController::class, 'uploadNewVersion'])->name('documents.new-version');
        Route::get('documents/{document}/versions', [DocumentController::class, 'versions'])->name('documents.versions');
        Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // User management & admin-only sections
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/logo', [SettingsController::class, 'uploadLogo'])->name('settings.upload-logo');
        Route::post('settings/favicon', [SettingsController::class, 'uploadFavicon'])->name('settings.upload-favicon');
        Route::delete('settings/logo', [SettingsController::class, 'removeLogo'])->name('settings.remove-logo');

        // Email
        Route::get('email', [EmailController::class, 'index'])->name('email.index');
        Route::put('email/settings', [EmailController::class, 'updateSettings'])->name('email.update-settings');
        Route::post('email/send-expiry-reminder', [EmailController::class, 'sendExpiryReminder'])->name('email.send-expiry-reminder');
        Route::post('email/send-declarations', [EmailController::class, 'sendDeclarations'])->name('email.send-declarations');
        Route::put('email/smtp', [EmailController::class, 'updateSmtpSettings'])->name('email.update-smtp');
        Route::post('email/smtp-test', [EmailController::class, 'testSmtpConnection'])->name('email.test-smtp');

        // Libri Sociali
        Route::prefix('libri-sociali')->name('libri-sociali.')->group(function () {
            Route::get('/', [RiunioneController::class, 'index'])->name('index');
            Route::get('/create', [RiunioneController::class, 'create'])->name('create');
            Route::post('/', [RiunioneController::class, 'store'])->name('store');
            Route::get('/{riunione}', [RiunioneController::class, 'show'])->name('show');
            Route::get('/{riunione}/edit', [RiunioneController::class, 'edit'])->name('edit');
            Route::put('/{riunione}', [RiunioneController::class, 'update'])->name('update');
            Route::delete('/{riunione}', [RiunioneController::class, 'destroy'])->name('destroy');

            // Status & documenti
            Route::patch('/{riunione}/status', [RiunioneController::class, 'advanceStatus'])->name('status');
            Route::post('/{riunione}/convocazione', [RiunioneController::class, 'uploadConvocazione'])->name('upload-convocazione');
            Route::post('/{riunione}/verbale', [RiunioneController::class, 'uploadVerbale'])->name('upload-verbale');
            Route::get('/{riunione}/convocazione/download', [RiunioneController::class, 'downloadConvocazione'])->name('download-convocazione');
            Route::get('/{riunione}/verbale/download', [RiunioneController::class, 'downloadVerbale'])->name('download-verbale');

            // Presenti
            Route::post('/{riunione}/partecipanti', [RiunioneController::class, 'storePartecipanti'])->name('partecipanti.store');

            // Delibere (nested)
            Route::post('/{riunione}/delibere', [DeliberaController::class, 'store'])->name('delibere.store');
        });

        // Delibere: update/delete con route model binding diretto
        Route::put('delibere/{delibera}', [DeliberaController::class, 'update'])->name('delibere.update');
        Route::delete('delibere/{delibera}', [DeliberaController::class, 'destroy'])->name('delibere.destroy');
    });

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Activity log (admin + manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });
});
