<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Journey\ClearLicenseAreaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys'])
    ->prefix('licenses')->as('licenses:')->group(function (): void {
        Route::post('/clear/area', ClearLicenseAreaController::class)->name(name: 'clear');
    });
