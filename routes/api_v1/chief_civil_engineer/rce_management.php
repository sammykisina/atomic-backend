<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\ChiefCivilEngineer\RCEManagement\IndexController;
use App\Http\Controllers\Domains\ChiefCivilEngineer\RCEManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rce-assignment,create-rce-assignment,edit-rce-assignment,delete-rce-assignment'])->prefix('rce-management')->as('rce-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', 'assign')->name(name: 'assign');
        // Route::patch('/{inspectionSchedule}/edit', 'edit')->name(name: 'edit');
        // Route::delete('/{inspectionSchedule}/delete', 'delete')->name(name: 'delete');
    });
});
