<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\Journeys\IndexController;
use App\Http\Controllers\Domains\Operator\Journeys\ManagementController as JourneysManagementController;
use App\Http\Controllers\Domains\Operator\Licenses\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,edit-journeys,delete-journeys,reject-journeys,accept-journeys'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");

        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('{journey}/notifications/{notification}/accept-request', 'acceptRequest')->name(name: 'accept-request');
            Route::post('{journey}/notifications/{notification}/decline-request', 'declineRequest')->name(name: 'decline-request');
            Route::post('{journey}/notifications/{notification}/reject-exit-line-request', 'rejectExitLineRequest')->name(name: 'reject-exit-line-request');
        });

        Route::controller(JourneysManagementController::class)->group(function (): void {
            Route::post('{journey}/notifications/{notification}/reject-exit-line-request', 'rejectExitLineRequest')->name(name: 'reject-exit-line-request');
            Route::post('{journey}/notifications/{notification}/accept-exit-line-request', 'acceptExitLineRequest')->name(name: 'accept-exit-line-request');
        });
    });
