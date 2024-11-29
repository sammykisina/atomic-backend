<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Journey\ClearLicenseAreaController;
use App\Http\Controllers\Domains\Driver\Journey\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys'])
    ->prefix('licenses')->as('licenses:')->group(function (): void {
        Route::post('/{license}/clear/area', ClearLicenseAreaController::class)->name(name: 'clear');

        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('/{license}/notifications/{notification}/accept-license-area-revoke-request', 'acceptRevokeLicenseAreaRequest')->name(name: 'accept-revoke-license-area-request');
            Route::post('/{license}/notifications/{notification}/reject-license-area-revoke-request', 'rejectRevokeLicenseAreaRequest')->name(name: 'reject-revoke-license-area-request');
        });
    });
