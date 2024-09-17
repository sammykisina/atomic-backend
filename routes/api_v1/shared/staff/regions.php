<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Regions\IndexController;
use App\Http\Controllers\Domains\Shared\Staff\Regions\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-regions'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");
    Route::controller(ManagementController::class)->group(function (): void {
        Route::get('{region}/show', 'show')->name(name: 'show');
    });
});
