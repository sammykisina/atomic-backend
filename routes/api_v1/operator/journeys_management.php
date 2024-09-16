<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\JourneyManagement\IndexController;
use App\Http\Controllers\Domains\Operator\JourneyManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,edit-journeys,delete-journeys,reject-journeys,accept-journeys'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");


        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('{journey}/driver/current-location', 'confirmDriverCurrentLocation')->name(name: 'driver-current-location');
        });
    });
