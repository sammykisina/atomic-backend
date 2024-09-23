<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Shifts\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Shifts\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-shifts,create-shifts,edit-shifts,delete-shifts'])->prefix('shifts')->as('desks:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::get('/{shift}/show', 'show')->name(name: 'show');
        Route::delete('/{shift}/delete', 'delete')->name(name: 'delete');
    });
});
