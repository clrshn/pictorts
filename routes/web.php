<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\TableReportController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\SavedFilterController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\TodoSubtaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Public document tracking (no auth required)
Route::post('/track/search', [TrackController::class, 'search'])->name('track.search');

Route::middleware(['auth', 'verified'])->group(function () {
    // Main authenticated application area. Most system modules live under this group.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/track-document', [TrackController::class, 'page'])->name('track.page');
    Route::get('/search', [GlobalSearchController::class, 'index'])->name('search.index');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::post('/documents/{document}/route', [DocumentController::class, 'route'])->name('documents.route');
    Route::post('/documents/{document}/receive', [DocumentController::class, 'receive'])->name('documents.receive');
    Route::get('/tracking-numbers', [DocumentController::class, 'trackingNumbers'])->name('documents.tracking-numbers');
    Route::get('/documents/{document}/files/{file}/preview', [DocumentController::class, 'previewFile'])->name('documents.files.preview');
    Route::get('/documents/{document}/files/{file}/download', [DocumentController::class, 'downloadFile'])->name('documents.files.download');

    // Financial
    Route::resource('financial', FinancialController::class);
    Route::post('/financial/{financial}/route', [FinancialController::class, 'route'])->name('financial.route');
    Route::post('/financial/{financial}/receive', [FinancialController::class, 'receive'])->name('financial.receive');
    Route::patch('/financial/{financial}/update-status', [FinancialController::class, 'updateStatus'])->name('financial.update-status');
    Route::get('/financial/{financial}/files/{file}/preview', [FinancialController::class, 'previewFile'])->name('financial.files.preview');
    Route::get('/financial/{financial}/files/{file}/download', [FinancialController::class, 'downloadFile'])->name('financial.files.download');

    // Ad hoc table reports
    Route::post('/table-reports', [TableReportController::class, 'store'])->name('table-reports.store');
    Route::get('/table-reports/{report}', [TableReportController::class, 'show'])->name('table-reports.show');

    // Todos
    Route::resource('todos', \App\Http\Controllers\TodoController::class);
    Route::patch('/todos/{todo}/update-status', [\App\Http\Controllers\TodoController::class, 'updateStatus'])->name('todos.updateStatus');
    Route::patch('/todos/{todo}/update-priority', [\App\Http\Controllers\TodoController::class, 'updatePriority'])->name('todos.updatePriority');
    Route::patch('/todos/{todo}/quick-update', [\App\Http\Controllers\TodoController::class, 'quickUpdate'])->name('todos.quickUpdate');

    // Administrative master-data pages are intentionally separated so regular users
    // cannot manage accounts or office definitions.
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Office Management (Admin only)
        Route::resource('offices', OfficeController::class);
    });

    // Notification routes support both the bell/dropdown UI and email/in-app testing.
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/test', [NotificationController::class, 'sendTestNotification'])->name('notifications.test');
    Route::post('/notifications/document', [NotificationController::class, 'sendDocumentNotification'])->name('notifications.document');
    Route::post('/notifications/financial', [NotificationController::class, 'sendFinancialNotification'])->name('notifications.financial');
    Route::post('/notifications/task', [NotificationController::class, 'sendTaskNotification'])->name('notifications.task');
    Route::post('/notifications/welcome', [NotificationController::class, 'sendWelcomeEmail'])->name('notifications.welcome');
    Route::post('/notifications/admins', [NotificationController::class, 'sendToAdmins'])->name('notifications.admins');
    Route::post('/notifications/office', [NotificationController::class, 'sendToOffice'])->name('notifications.office');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/approvals/request', [ApprovalController::class, 'requestApproval'])->name('approvals.request');
    Route::post('/approvals/review', [ApprovalController::class, 'review'])->name('approvals.review');
    Route::post('/pins', [PinController::class, 'store'])->name('pins.store');
    Route::delete('/pins', [PinController::class, 'destroy'])->name('pins.destroy');
    Route::post('/saved-filters', [SavedFilterController::class, 'store'])->name('saved-filters.store');
    Route::delete('/saved-filters/{savedFilter}', [SavedFilterController::class, 'destroy'])->name('saved-filters.destroy');
    Route::post('/todos/{todo}/subtasks', [TodoSubtaskController::class, 'store'])->name('todos.subtasks.store');
    Route::match(['patch', 'put'], '/todos/{todo}/subtasks/{subtask}', [TodoSubtaskController::class, 'update'])->name('todos.subtasks.update');
    Route::delete('/todos/{todo}/subtasks/{subtask}', [TodoSubtaskController::class, 'destroy'])->name('todos.subtasks.destroy');

    // Email Testing
    Route::get('/test-email', [TestEmailController::class, 'test'])->name('test.email');
    Route::post('/test-email/send', [TestEmailController::class, 'test'])->name('test.email.send');
    Route::post('/test-email/send-without-auth', [TestEmailController::class, 'testWithoutAuth'])->name('test.email.send.without.auth');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
    Route::post('/profile/photo/remove', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');
});

require __DIR__.'/auth.php';
