<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\TrackAttendant\InspectionIssueManagementController;
use App\Http\Controllers\Domains\TrackAttendant\Inspections\IndexController;
use App\Http\Controllers\Domains\TrackAttendant\Inspections\InspectionManagementController;
use App\Http\Controllers\Domains\TrackAttendant\InspectionScheduleController;
use App\Http\Controllers\Domains\TrackAttendant\IssueNames\IssueNamesManagement;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:create-inspections,read-inspections'])->prefix('inspections')->as('inspections:')->group(function (): void {
    // INSPECTIONS
    Route::get('/', IndexController::class)->name(name: 'index');
    Route::get('/inspection-schedule', InspectionScheduleController::class)->name('inspection-schedule');

    Route::controller(InspectionManagementController::class)->group(function (): void {
        Route::patch('/{inspectionSchedule}/{notification}/confirm', 'confirmInspectionSchedule')->name(name: 'confirm');
        Route::post('/', 'createInspection')->name(name: 'create');
        Route::get('{inspection}/show', 'show')->name(name: 'show');

        Route::patch('{inspection}/stop', 'stop')->name(name: 'stop');

        Route::patch('{inspection}/abortJourney', 'abortJourney')->name(name: 'abort-journey');
    });

    Route::controller(InspectionIssueManagementController::class)->group(function (): void {
        Route::post('{inspection}/issues', 'create')->name(name: 'create-issue');
    });


    // ISSUE NAMES
    Route::controller(IssueNamesManagement::class)->group(function (): void {
        Route::get('/issues-names', 'index')->name(name: 'index-issue-names');
        Route::post('/issues-names', 'create')->name(name: 'issue-name-create');
    });
});
