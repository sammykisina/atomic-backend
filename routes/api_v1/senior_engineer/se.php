<?php

declare(strict_types=1);


use App\Http\Controllers\Domains\SeniorEngineer\InspectionsIndexController;
use App\Http\Controllers\Domains\SeniorEngineer\RSTIIssuesManagement;
use App\Http\Controllers\Domains\SeniorEngineer\ShowMySEAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-se-assignments'])->group(function (): void {
    Route::get('/assignments', ShowMySEAssignmentController::class)->name(name: "my-se-assignments");
});

Route::middleware(['auth:sanctum', 'ability:read-rsti-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "rsti-inspections");
});

Route::middleware(['auth:sanctum', 'ability:read-rsti-issues,approve-speed-restriction'])->group(function (): void {
    Route::controller(RSTIIssuesManagement::class)->group(function (): void {
        Route::patch('/{issueArea}/approve-speed-restriction-suggestion', action: 'approveSpeedRestrictionSuggestion')->name(name: 'approve-speed-restriction-suggestion');

        Route::patch(uri: '/{issueArea}/add-speed-restriction', action: 'addSpeedRestriction')->name(name: 'add-speed-restriction');
    });

});
