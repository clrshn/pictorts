<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Public document tracking (no auth required)
Route::post('/track/search', [TrackController::class, 'search'])->name('track.search');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/track-document', [TrackController::class, 'page'])->name('track.page');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::post('/documents/{document}/route', [DocumentController::class, 'route'])->name('documents.route');
    Route::post('/documents/{document}/receive', [DocumentController::class, 'receive'])->name('documents.receive');
    Route::get('/tracking-numbers', [DocumentController::class, 'trackingNumbers'])->name('documents.tracking-numbers');

    // Financial
    Route::resource('financial', FinancialController::class);
    Route::post('/financial/{financial}/route', [FinancialController::class, 'route'])->name('financial.route');
    Route::post('/financial/{financial}/receive', [FinancialController::class, 'receive'])->name('financial.receive');

    // Todos
    Route::resource('todos', \App\Http\Controllers\TodoController::class);
    Route::patch('/todos/{todo}/quick-update', [\App\Http\Controllers\TodoController::class, 'quickUpdate'])->name('todos.quickUpdate');

    // User Management (Admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
