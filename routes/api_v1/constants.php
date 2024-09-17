<?php

declare(strict_types=1);

use App\Http\Controllers\Constants\ApplicationModulesController;
use App\Http\Controllers\Constants\DeskController;
use Illuminate\Support\Facades\Route;

// APPLICATION MODULES
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/application/modules', ApplicationModulesController::class)->name(name: "application-modules");

    Route::controller(DeskController::class)->prefix('desks')->as('desks:')->group(function (): void {
        Route::get('/', 'index')->name(name: 'index');
        Route::get('/{desk}/show', 'show')->name(name: 'show');
    });
});
