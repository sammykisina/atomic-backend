<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionAdmin\Regions\IndexController;
use App\Http\Controllers\Domains\RegionAdmin\Regions\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-regions,edit-regions,delete-regions'])
    ->prefix('regions')->as('regions:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");

        Route::controller(ManagementController::class)->group(function (): void {
            Route::get('{region}/show', action: 'show')->name(name: "show");
            Route::post('/', action: 'create')->name(name: "create");
            Route::patch('{region}/edit', action: 'edit')->name(name: "edit");
            Route::delete('{region}/delete', action: 'delete')->name(name: "delete");
        });
    });
