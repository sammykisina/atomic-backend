<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Journey\ClearLicenseAreaController;
use App\Http\Controllers\Domains\Operator\Licenses\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys'])
    ->prefix('licenses')->as('licenses:')->group(function (): void {
        Route::post('/{license}/clear/area', ClearLicenseAreaController::class)->name(name: 'clear');
    });

Route::middleware(['auth:sanctum', 'ability:read-journeys'])->group(function (): void {
    Route::controller(ManagementController::class) ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::post('{journey}/assign/license', 'assignLicense')->name(name: 'assign-license');
        Route::post('{journey}/assign/sos-license', 'sosLicense')->name(name: 'assign-sos-license');

        Route::patch('/{journey}/licenses/{license}/notifications/{notification}/confirm-license', 'confirmLicense')->name(name: 'operator-confirm-license');
    });


    Route::controller(ManagementController::class) ->prefix('licenses')->as('licenses:')->group(function (): void {
        Route::patch('{license}/revoke-area', 'revokeLicenseArea')->name(name: 'revoke-license-area');
    });
});
