<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ClaimVerificationController as AdminClaimVerificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportReviewController as AdminReportReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClaimRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\ItemDiscoveryController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'welcome'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:20,1');

    // Answers 404 unless AUTH_MODE=self_register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Any Role)
|--------------------------------------------------------------------------
| auth             -> must be logged in
| active           -> account must not be deactivated
| password.changed -> admin-created accounts must first replace temporary password
*/
Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Role-based home redirect
    Route::get('/home', [DashboardController::class, 'home'])->name('home');

    // Regular user dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lost Item Reporting (Module 2)
    Route::get('/report-lost', [LostItemController::class, 'create'])->name('lost-items.create');
    Route::post('/lost-items', [LostItemController::class, 'store'])->name('lost-items.store');
    Route::get('/lost-items/{lostItem}', [LostItemController::class, 'show'])->name('lost-items.show');

    // Found Item Reporting (Module 3)
    Route::get('/report-found', [FoundItemController::class, 'create'])->name('found-items.create');
    Route::post('/found-items', [FoundItemController::class, 'store'])->name('found-items.store');
    Route::get('/found-items/{foundItem}', [FoundItemController::class, 'show'])->name('found-items.show');

    // Item Discovery & Search (Module 4 - Phase 7)
    Route::get('/items', [ItemDiscoveryController::class, 'index'])->name('items.index');

    // Claim Workflow & Tracking (Module 5 - Phase 8)
    Route::get('/found-items/{foundItem}/claim', [ClaimRequestController::class, 'create'])->name('claims.create');
    Route::post('/claims', [ClaimRequestController::class, 'store'])->name('claims.store')->middleware('throttle:10,1');
    Route::get('/my-claims', [ClaimRequestController::class, 'index'])->name('claims.index');
    Route::get('/my-claims/{claim}', [ClaimRequestController::class, 'show'])->name('claims.show');
    Route::post('/my-claims/{claim}/respond', [ClaimRequestController::class, 'respond'])
        ->middleware('throttle:10,1')
        ->name('claims.respond');

    // In-App Notification System (Module 6 - Phase 9)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('profile.password.update');
});

/*
|--------------------------------------------------------------------------
| Administrator Routes (/admin/...)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'password.changed', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        // Reports, Analytics & Exports (Module 8 - Phase 11)
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/reports/export-csv', [AdminAnalyticsController::class, 'exportReportsCsv'])->name('reports.export-csv');
        Route::get('/claims/export-csv', [AdminAnalyticsController::class, 'exportClaimsCsv'])->name('claims.export-csv');

        // Report Review (Module 7 - Phase 10)
        Route::get('/reports', [AdminReportReviewController::class, 'index'])->name('reports.index');
        Route::get('/reports/lost/{lostItem}', [AdminReportReviewController::class, 'showLost'])->name('reports.show-lost');
        Route::get('/reports/found/{foundItem}', [AdminReportReviewController::class, 'showFound'])->name('reports.show-found');
        Route::post('/reports/lost/{lostItem}/approve', [AdminReportReviewController::class, 'approveLost'])->name('reports.lost.approve');
        Route::post('/reports/lost/{lostItem}/reject', [AdminReportReviewController::class, 'rejectLost'])->name('reports.lost.reject');
        Route::post('/reports/lost/{lostItem}/correct', [AdminReportReviewController::class, 'correctLost'])->name('reports.lost.correct');
        Route::post('/reports/found/{foundItem}/approve', [AdminReportReviewController::class, 'approveFound'])->name('reports.found.approve');
        Route::post('/reports/found/{foundItem}/reject', [AdminReportReviewController::class, 'rejectFound'])->name('reports.found.reject');
        Route::post('/reports/found/{foundItem}/correct', [AdminReportReviewController::class, 'correctFound'])->name('reports.found.correct');

        // Claim Verification (Module 7 - Phase 10)
        Route::get('/claims', [AdminClaimVerificationController::class, 'index'])->name('claims.index');
        Route::get('/claims/{claim}', [AdminClaimVerificationController::class, 'show'])->name('claims.show');
        Route::post('/claims/{claim}/under-review', [AdminClaimVerificationController::class, 'underReview'])->name('claims.under-review');
        Route::post('/claims/{claim}/request-info', [AdminClaimVerificationController::class, 'requestInfo'])->name('claims.request-info');
        Route::post('/claims/{claim}/approve', [AdminClaimVerificationController::class, 'approve'])->name('claims.approve');
        Route::post('/claims/{claim}/reject', [AdminClaimVerificationController::class, 'reject'])->name('claims.reject');
        Route::post('/claims/{claim}/return', [AdminClaimVerificationController::class, 'recordReturn'])->name('claims.record-return');

        // User Management (Module 7 - Phase 10)
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

        // Category Management (Module 7 - Phase 10)
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggle-active');

        // Append-Only Activity Audit Logs (Module 8 - Phase 11)
        Route::get('/logs', [AdminActivityLogController::class, 'index'])->name('logs.index');
    });
