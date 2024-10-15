<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalPermanentWayInspector\InspectionsIndexController;
use App\Http\Controllers\Domains\RegionalPermanentWayInspector\ShowMyRPWIAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-pwi-assignment'])->group(function (): void {
    Route::get('/assignments', ShowMyRPWIAssignmentController::class)->name(name: "show-rpwi-assignments");
});

Route::middleware(['auth:sanctum', 'ability:read-pwi-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "show-pwi-assignments");
});
