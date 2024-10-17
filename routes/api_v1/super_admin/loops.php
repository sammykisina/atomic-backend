<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Loops\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Loops\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-loops,create-loops,edit-loops,delete-loops'])->group(function (): void {
    Route::get('loops', IndexController::class)->name(name: 'index');

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('lines/{line}/stations/{station}/loops', 'create')->name(name: 'create');
        Route::patch('/loops/{loop}/edit', 'edit')->name(name: 'edit');
        Route::get('loops/{loop}/show', 'show')->name(name: 'show');
    });
});
