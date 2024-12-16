<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Obcs\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Obcs\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-obcs,create-obcs,edit-obcs,delete-obcs'])->prefix('obcs')->as('obcs:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{obc}/edit', 'edit')->name(name: 'edit');
        Route::get('/{obc}/show', 'show')->name(name: 'show');
        Route::delete('/{obc}/delete', 'delete')->name(name: 'delete');
    });
});
