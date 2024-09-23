<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Setup\Desks\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Setup\Desks\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-desks,create-desks,edit-desks,delete-desks'])->prefix('desks')->as('desks:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{desk}/edit', 'edit')->name(name: 'edit');
        Route::get('/{desk}/show', 'show')->name(name: 'show');
    });
});
