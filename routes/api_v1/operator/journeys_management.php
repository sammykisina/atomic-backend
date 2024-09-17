<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\JourneyManagement\IndexController;
use App\Http\Controllers\Domains\Operator\License\ManagementController as LicenseManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,edit-journeys,delete-journeys,reject-journeys,accept-journeys'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");

        Route::controller(LicenseManagementController::class)->group(function (): void {
            Route::post('{journey}/accept', 'acceptJourney')->name(name: 'accept-journey');
        });
    });
