<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Driver\Panic\ActivePanicController;
use App\Http\Controllers\Domains\Driver\Panic\IndexController;
use App\Http\Controllers\Domains\Driver\Panic\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys'])
    ->prefix('panics')->as('panics:')->group(function (): void {
        Route::post('/journey/{journey}', StoreController::class)->name(name: "create-panic");
        Route::get('active', ActivePanicController::class)->name(name: "active-panic");
        Route::get('/', IndexController::class)->name(name: "panics");
    });
