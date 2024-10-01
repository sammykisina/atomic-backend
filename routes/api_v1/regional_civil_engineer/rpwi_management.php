<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalCivilEngineer\RPWIManagement\IndexController;
use App\Http\Controllers\Domains\RegionalCivilEngineer\RPWIManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rpwi-assignment,create-rpwi-assignment,edit-rpwi-assignment,delete-rpwi-assignment'])->prefix('rpwi-management')->as('rpwi-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', action: 'assign')->name(name: 'assign');
        // Route::patch('/{inspectionSchedule}/edit', 'edit')->name(name: 'edit');
        // Route::delete('/{inspectionSchedule}/delete', 'delete')->name(name: 'delete');
    });
});
