<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionAdmin\Regions\LineRegionManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:line-regions-divisions'])->group(function (): void {
    Route::controller(LineRegionManagementController::class)->group(function (): void {
        Route::post('/lines/{line}/regions-divisions', action: 'lineRegionsDivisions')->name(name: "line-regions-divisions");
    });
});
