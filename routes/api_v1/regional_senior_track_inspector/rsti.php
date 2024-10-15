<?php

declare(strict_types=1);



use App\Http\Controllers\Domains\RegionalSeniorTrackInspector\InspectionsIndexController;
use App\Http\Controllers\Domains\RegionalSeniorTrackInspector\ShowMyRSTIAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rsti-assignments'])->group(function (): void {
    Route::get('/assignments', ShowMyRSTIAssignmentController::class)->name(name: "my-rsti-assignments");
});

Route::middleware(['auth:sanctum', 'ability:read-sti-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "sti-inspections");
});
