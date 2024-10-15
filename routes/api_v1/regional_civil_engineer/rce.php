<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalCivilEngineer\InspectionsIndexController;
use App\Http\Controllers\Domains\RegionalCivilEngineer\RPWIIssuesManagement;
use App\Http\Controllers\Domains\RegionalCivilEngineer\ShowMyRCEAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rpwi-assignment'])->group(function (): void {
    Route::get('/assignments', ShowMyRCEAssignmentController::class)->name(name: "show-rce-assignments");
});

Route::middleware(['auth:sanctum', 'ability:read-rpwi-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "read-rpwi-inspections");
});

Route::middleware(['auth:sanctum', 'ability:read-rpwi-issues,approve-speed-restriction'])->group(function (): void {
    Route::controller(RPWIIssuesManagement::class)->group(function (): void {
        Route::patch('/{issueArea}/approveSpeedRestrictionSuggestion', action: 'approveSpeedRestrictionSuggestion')->name(name: 'approve-speed-restriction-suggestion');

        Route::patch(uri: '/{issueArea}/add-speed-restriction', action: 'addSpeedRestriction')->name(name: 'add-speed-restriction');
    });

});
