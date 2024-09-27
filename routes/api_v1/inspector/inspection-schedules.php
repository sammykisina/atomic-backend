<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Inspector\InspectionScheduleManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:confirm-inspection-schedules'])->prefix('inspection-schedules')->as('inspection-schedules:')->group(function (): void {
    Route::controller(InspectionScheduleManagementController::class)->group(function (): void {
        Route::patch('/{inspectionSchedule}/{notification}/confirm', 'confirmInspectionSchedule')->name(name: 'confirm');
    });
});
