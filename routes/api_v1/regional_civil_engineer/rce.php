<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalCivilEngineer\InspectionsIndexController;
use App\Http\Controllers\Domains\RegionalCivilEngineer\ShowMyRCEAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rpwi-assignment'])->group(function (): void {
    Route::get('/assignments', ShowMyRCEAssignmentController::class)->name(name: "show-rce-assignments");
});

Route::middleware(['auth:sanctum', 'ability:read-rce-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "show-rce-assignments");
});
