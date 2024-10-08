<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Dashboards\CCEDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\GangManDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\PWADashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\RCEDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\RPWIDashboardController;
use Illuminate\Support\Facades\Route;

// CCE
Route::middleware(['auth:sanctum'])->prefix('/chief-civil-engineer')->as('chief-civil-engineer:')->group(function (): void {
    Route::get('/dashboard', CCEDashboardController::class)->name(name: 'cce-dashboard');
});


// RCE
Route::middleware(['auth:sanctum'])->prefix('/regional-civil-engineer')->as('regional-civil-engineer:')->group(function (): void {
    Route::get('/dashboard', RCEDashboardController::class)->name(name: 'rce-dashboard');
});

// RPWI
Route::middleware(['auth:sanctum'])->prefix('/regional-permanent-way-inspector')->as('regional-permanent-way-inspector:')->group(function (): void {
    Route::get('/dashboard', RPWIDashboardController::class)->name(name: 'rpwi-dashboard');
});

// PWI
Route::middleware(['auth:sanctum'])->prefix('/permanent-way-inspector')->as('permanent-way-inspector:')->group(function (): void {
    Route::get('/dashboard', PWADashboardController::class)->name(name: 'pwa-dashboard');
});


// GANG MAN
Route::middleware(['auth:sanctum'])->prefix('/gang-man')->as('gang-man:')->group(function (): void {
    Route::get('/dashboard', GangManDashboardController::class)->name(name: 'gang-man-dashboard');
});
