<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / guest routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => auth()->check() ? redirect()->route('home') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:20,1');

    // Answers 404 unless AUTH_MODE=self_register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes (any role)
|--------------------------------------------------------------------------
| auth             -> must be logged in
| active           -> account must not be deactivated
| password.changed -> admin-created accounts must first replace the temporary password
*/
Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Sends each role to its own home page.
    Route::get('/home', fn () => redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard'))
        ->name('home');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Quick-action & navigation endpoints (full controllers implemented in Phases 6, 7, 8)
    Route::get('/items', fn () => redirect()->route('dashboard')->with('info', 'Search and item discovery is being enabled in Phase 7.'))->name('items.index');
    Route::get('/report-lost', fn () => redirect()->route('dashboard')->with('info', 'Lost item reporting form is being enabled in Phase 6.'))->name('lost-items.create');
    Route::get('/report-found', fn () => redirect()->route('dashboard')->with('info', 'Found item reporting form is being enabled in Phase 6.'))->name('found-items.create');
    Route::get('/my-claims', fn () => redirect()->route('dashboard')->with('info', 'Claim tracking is being enabled in Phase 8.'))->name('claims.index');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('profile.password.update');
});

/*
|--------------------------------------------------------------------------
| Administrator routes  (/admin/...)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'password.changed', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
    });
