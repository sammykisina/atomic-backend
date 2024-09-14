<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Journey\IndexController;
use App\Http\Controllers\Domains\Driver\Journey\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys,request-journeys,edit-journeys,delete-journeys'])
    ->prefix('journeys')->as('journeys:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");


        Route::controller(ManagementController::class)->group(function (): void {
            Route::post('/', 'create')->name(name: 'create');
            Route::patch('/{journey}/edit', 'edit')->name(name: 'edit');
        });
    });
