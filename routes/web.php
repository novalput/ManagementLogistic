<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Items Management (CRUD)
    Route::resource('items', ItemController::class)->except(['index', 'show']);

    // Borrowing (View & Create Request)
    Route::get('/borrow/{type}', [BorrowingController::class, 'create'])
        ->where('type', 'consumable|returnable')
        ->name('borrow.create');
    Route::post('/borrow/{type}', [BorrowingController::class, 'store'])
        ->where('type', 'consumable|returnable')
        ->name('borrow.store');
    Route::post('/borrow/scan', [BorrowingController::class, 'scanItem'])->name('borrow.scan');

    // Returns Management
    Route::controller(ReturnController::class)->prefix('return')->name('return')->group(function () {
        Route::get('/', 'index'); // name: return
        Route::post('/verify', 'verify')->name('.verify');
        Route::get('/{borrowingId}', 'show')->name('.show');
        Route::post('/{borrowingId}/notes', 'updateNotes')->name('.notes');
        Route::post('/confirm', 'confirm')->name('.confirm');
    });

    // Borrowing Management
    Route::patch('/borrowings/{borrowing}/approve', [BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::delete('/borrowings/{borrowing}/reject', [BorrowingController::class, 'reject'])->name('borrowings.reject');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Items (Read-only for all authenticated users)
    Route::get('/items', [ItemController::class, 'index'])->name('items.list');
    Route::get('items/{item}/barcode-preview', [ItemController::class, 'previewBarcode'])->name('items.previewBarcode');
    Route::get('items/{item}/barcode', [ItemController::class, 'downloadBarcode'])->name('items.downloadBarcode');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
});

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::post('/avatar', 'updateAvatar')->name('avatar.update');
        Route::delete('/avatar', 'removeAvatar')->name('avatar.remove');
    });
});

require __DIR__.'/auth.php';
