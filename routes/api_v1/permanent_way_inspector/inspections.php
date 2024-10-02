<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\PermanentWayInspector\Inspections\IndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-inspections'])->prefix('inspections')->as('inspections:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    // Route::controller(ManagementController::class)->group(function (): void {
    //     Route::post('/', 'create')->name(name: 'create');
    //     Route::patch('/{inspectionSchedule}/edit', 'edit')->name(name: 'edit');
    //     Route::delete('/{inspectionSchedule}/delete', 'delete')->name(name: 'delete');
    // });
});
