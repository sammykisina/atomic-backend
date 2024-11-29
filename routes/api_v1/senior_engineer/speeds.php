<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SeniorEngineer\Speeds\IndexController;
use App\Http\Controllers\Domains\SeniorEngineer\Speeds\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-speeds,create-speeds,edit-speeds,delete-speeds'])->prefix('speeds')->as('speeds:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::delete('{speed}/delete', action: 'delete')->name(name: 'delete');
        Route::patch('{speed}/adjust-speed-restriction', action: 'adjustSpeedRestriction')->name(name: 'adjust-speed-restriction');
    });
});
