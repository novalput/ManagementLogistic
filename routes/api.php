<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ReportController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('items', ItemController::class)->names('api.items');
    Route::get('/items/scan/{code}', [ItemController::class, 'findByBarcode']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/borrowings', [BorrowController::class, 'index']);
    Route::post('/borrowings', [BorrowController::class, 'store']);
    Route::get('/borrowings/{id}', [BorrowController::class, 'show']);
    Route::post('/borrowings/{id}/return', [BorrowController::class, 'processReturn']);
    Route::get('/reports', [ReportController::class, 'index']);
});
