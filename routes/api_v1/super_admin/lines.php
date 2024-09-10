<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Lines\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Lines\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:read-lines,create-lines,update-lines,delete-lines'])->prefix('lines')->as('lines:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name:'create');
        Route::patch('/{line}/edit', 'edit')->name(name:'edit');
    });
});
