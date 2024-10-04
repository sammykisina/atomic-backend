<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Dashboards\GangManDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\PWADashboardController;
use Illuminate\Support\Facades\Route;

// PWI
Route::middleware(['auth:sanctum'])->prefix('/permanent-way-inspector')->as('permanent-way-inspector:')->group(function (): void {
    Route::get('/dashboard', PWADashboardController::class)->name(name: 'pwa-dashboard');
});


// GANG MAN
Route::middleware(['auth:sanctum'])->prefix('/gang-man')->as('gang-man:')->group(function (): void {
    Route::get('/dashboard', GangManDashboardController::class)->name(name: 'gang-man-dashboard');
});
