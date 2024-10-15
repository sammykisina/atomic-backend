<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalSeniorTrackInspector\STIManagement\IndexController;
use App\Http\Controllers\Domains\RegionalSeniorTrackInspector\STIManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-sti-assignments,create-sti-assignments,edit-sti-assignments,delete-sti-assignments'])->prefix('sti-management')->as('sti-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', action: 'assign')->name(name: 'assign');
    });
});
