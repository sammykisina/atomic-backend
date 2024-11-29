<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SeniorEngineer\RSTIManagement\IndexController;
use App\Http\Controllers\Domains\SeniorEngineer\RSTIManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rsti-assignments,create-rsti-assignments,edit-rsti-assignments,delete-rsti-assignments'])->prefix('rsti-management')->as('rsti-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', action: 'assign')->name(name: 'assign');
    });
});
