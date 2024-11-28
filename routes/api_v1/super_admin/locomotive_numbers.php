<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\LocomotiveNumbers\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\LocomotiveNumbers\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-locomotive-numbers,create-locomotive-numbers,edit-locomotive-numbers,delete-locomotive-numbers'])->prefix('locomotive-numbers')->as('locomotive-numbers:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{locomotiveNumber}/edit', 'edit')->name(name: 'edit');
        Route::get('/{locomotiveNumber}/show', 'show')->name(name: 'show');
    });
});
