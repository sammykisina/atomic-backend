<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Inspector\InspectionIssueManagementController;
use App\Http\Controllers\Domains\Inspector\Inspections\IndexController;
use App\Http\Controllers\Domains\Inspector\Inspections\InspectionManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:confirm-inspection-schedules,create-inspections,read-inspections'])->prefix('inspections')->as('inspections:')->group(function (): void {
    // INSPECTIONS
    Route::get('/', IndexController::class)->name(name: 'index');

    Route::controller(InspectionManagementController::class)->group(function (): void {
        Route::patch('/{inspectionSchedule}/{notification}/confirm', 'confirmInspectionSchedule')->name(name: 'confirm');
        Route::post('/', 'createInspection')->name(name: 'create');
    });

    Route::controller(InspectionIssueManagementController::class)->group(function (): void {
        Route::post('{inspection}/issues', 'create')->name(name: 'create');
    });
});
