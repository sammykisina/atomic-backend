<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Stations\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Stations\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-stations,create-stations,update-stations,delete-stations'])->prefix('stations')->as('stations:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{station}/edit', 'edit')->name(name: 'edit');
        Route::get('/{station}/show', 'show')->name(name: 'show');
    });
});
