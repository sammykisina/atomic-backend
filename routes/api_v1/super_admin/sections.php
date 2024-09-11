<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Sections\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Sections\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:read-sections,create-sections,update-sections,delete-sections'])->prefix('sections')->as('sections:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name:'create');
        Route::patch('/{section}/edit', 'edit')->name(name:'edit');
    });
});
