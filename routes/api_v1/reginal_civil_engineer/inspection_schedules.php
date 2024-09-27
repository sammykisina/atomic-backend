<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\ReginalCivilEngineer\InspectionSchedules\IndexController;
use App\Http\Controllers\Domains\ReginalCivilEngineer\InspectionSchedules\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-inspection-schedules,create-inspection-schedules,edit-inspection-schedules,delete-inspection-schedules'])->prefix('inspection-schedules')->as('inspection-schedules:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{inspectionSchedule}/edit', 'edit')->name(name: 'edit');
        Route::delete('/{inspectionSchedule}/delete', 'delete')->name(name: 'delete');
    });
});
