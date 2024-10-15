<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\PrincipleEngineer\SEManagement\IndexController;
use App\Http\Controllers\Domains\PrincipleEngineer\SEManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-se-assignments,create-se-assignments,edit-se-assignments,delete-se-assignments'])->prefix('se-management')->as('se-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', 'assign')->name(name: 'assign');
    });
});
