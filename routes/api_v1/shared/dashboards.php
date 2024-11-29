<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Dashboards\PEDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\RSTIDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\SEDashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\STADashboardController;
use App\Http\Controllers\Domains\Shared\Dashboards\STIDashboardController;
use Illuminate\Support\Facades\Route;

// PE
Route::middleware(['auth:sanctum'])->prefix('/principal-engineer')->as('principal-engineer:')->group(function (): void {
    Route::get('/dashboard', PEDashboardController::class)->name(name: 'pe-dashboard');
});


// SE
Route::middleware(['auth:sanctum'])->prefix('/senior-engineer')->as('senior-engineer:')->group(function (): void {
    Route::get('/dashboard', SEDashboardController::class)->name(name: 'se-dashboard');
});

// RSTI
Route::middleware(['auth:sanctum'])->prefix('/regional-senior-track-inspector')->as('regional-senior-track-inspector:')->group(function (): void {
    Route::get('/dashboard', RSTIDashboardController::class)->name(name: 'rsti-dashboard');
});

// STI
Route::middleware(['auth:sanctum'])->prefix('/senior-track-inspector')->as('senior-track-inspector:')->group(function (): void {
    Route::get('/dashboard', STIDashboardController::class)->name(name: 'sti-dashboard');
});


// STA
Route::middleware(['auth:sanctum'])->prefix('/senior-track-attendant')->as('senior-track-attendant:')->group(function (): void {
    Route::get('/dashboard', STADashboardController::class)->name(name: 'senior-track-attendant-dashboard');
});
