<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TestEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
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
    Route::patch('/financial/{financial}/update-status', [FinancialController::class, 'updateStatus'])->name('financial.update-status');

    // Test route
    Route::patch('/test-update-status', function (Request $request) {
        try {
            $todo = \App\Models\Todo::find(1);
            
            if (!$todo) {
                return response()->json(['error' => 'Todo not found'], 404);
            }
            
            $validated = $request->validate([
                'status' => 'required|in:pending,on-going,done,cancelled',
            ]);

            $todo->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true, 
                'status' => $validated['status']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Todos
    Route::resource('todos', \App\Http\Controllers\TodoController::class);
    Route::patch('/todos/{todo}/update-status', [\App\Http\Controllers\TodoController::class, 'updateStatus'])->name('todos.updateStatus');
    Route::patch('/todos/{todo}/update-priority', [\App\Http\Controllers\TodoController::class, 'updatePriority'])->name('todos.updatePriority');
    Route::patch('/todos/{todo}/quick-update', [\App\Http\Controllers\TodoController::class, 'quickUpdate'])->name('todos.quickUpdate');

    // User Management (Admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Office Management (Admin only)
        Route::resource('offices', OfficeController::class);
    });

    // Email Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/test', [NotificationController::class, 'sendTestNotification'])->name('notifications.test');
    Route::post('/notifications/document', [NotificationController::class, 'sendDocumentNotification'])->name('notifications.document');
    Route::post('/notifications/financial', [NotificationController::class, 'sendFinancialNotification'])->name('notifications.financial');
    Route::post('/notifications/task', [NotificationController::class, 'sendTaskNotification'])->name('notifications.task');
    Route::post('/notifications/welcome', [NotificationController::class, 'sendWelcomeEmail'])->name('notifications.welcome');
    Route::post('/notifications/admins', [NotificationController::class, 'sendToAdmins'])->name('notifications.admins');
    Route::post('/notifications/office', [NotificationController::class, 'sendToOffice'])->name('notifications.office');

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
