<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyOfficerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShareholderController;
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

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Companies
    Route::middleware('permission:companies')->group(function () {
        Route::resource('companies', CompanyController::class);

        // Nested: Officers
        Route::post('companies/{company}/officers', [CompanyOfficerController::class, 'store'])->name('companies.officers.store');
        Route::put('officers/{officer}', [CompanyOfficerController::class, 'update'])->name('officers.update');
        Route::delete('officers/{officer}', [CompanyOfficerController::class, 'destroy'])->name('officers.destroy');

        // Nested: Shareholders
        Route::post('companies/{company}/shareholders', [ShareholderController::class, 'store'])->name('companies.shareholders.store');
        Route::put('shareholders/{shareholder}', [ShareholderController::class, 'update'])->name('shareholders.update');
        Route::delete('shareholders/{shareholder}', [ShareholderController::class, 'destroy'])->name('shareholders.destroy');
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

    // User management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
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
