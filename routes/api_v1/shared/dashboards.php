<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Dashboards\PWADashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('/permanent-way-inspector')->as('permanent-way-inspector:')->group(function (): void {
    Route::get('/dashboard', PWADashboardController::class)->name(name: 'pwa-dashboard');
});
