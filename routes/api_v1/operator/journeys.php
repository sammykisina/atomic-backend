<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\Journeys\IndexController;
use App\Http\Controllers\Domains\Operator\Licenses\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,edit-journeys,delete-journeys,reject-journeys,accept-journeys'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");

        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('{journey}/notifications/{notification}/accept-request', 'acceptRequest')->name(name: 'accept-request');
        });
    });
