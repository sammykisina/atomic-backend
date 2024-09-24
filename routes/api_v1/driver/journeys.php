<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Journey\IndexController;
use App\Http\Controllers\Domains\Driver\Journey\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,request-journeys,edit-journeys,delete-journeys,confirm-licenses'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");


        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('/', 'create')->name(name: 'create');
            Route::patch('/{journey}/edit', 'edit')->name(name: 'edit');
            Route::get('/{journey}/show', 'show')->name(name: 'show');

            Route::patch('/{journey}/licenses/{license}/notifications/{notification}/confirm-license', 'confirmLicense')->name(name: 'confirm-license');

            Route::patch('/{journey}/licenses/request', 'requestLicense')->name(name: 'request-license');

            Route::patch('/{journey}/end', 'endJourney')->name(name: 'end-journey');
        });
    });
