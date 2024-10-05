<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalPermanentWayInspector\PWIManagement\IndexController;
use App\Http\Controllers\Domains\RegionalPermanentWayInspector\PWIManagement\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-pwi-assignment,create-pwi-assignment,edit-pwi-assignment,delete-pwi-assignment'])->prefix('pwi-management')->as('pwi-management:')->group(function (): void {
    Route::get('/assignments', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/assign', action: 'assign')->name(name: 'assign');
    });
});
