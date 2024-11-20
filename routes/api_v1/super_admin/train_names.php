<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\TrainNames\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\TrainNames\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-train-names,create-train-names,edit-train-names,delete-train-names'])->prefix('train-names')->as('train-names:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{trainName}/edit', 'edit')->name(name: 'edit');
        Route::get('/{trainName}/show', 'show')->name(name: 'show');
        Route::delete('/{trainName}/delete', 'delete')->name(name: 'delete');
    });
});
