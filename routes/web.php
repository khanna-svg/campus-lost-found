<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\LostItemController;
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

    // Active placeholders until Phases 7, 8
    Route::get('/items', [DashboardController::class, 'placeholderItems'])->name('items.index');
    Route::get('/my-claims', [DashboardController::class, 'placeholderClaims'])->name('claims.index');

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
    });
