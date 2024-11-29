<?php

declare(strict_types=1);


use App\Http\Controllers\Domains\SuperAdmin\Trains\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Trains\ManagementController;
use App\Http\Controllers\Domains\SuperAdmin\Trains\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-trains,create-trains,edit-trains,delete-trains'])->prefix('trains')->as('trains:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");
    Route::get('/search', SearchController::class)->name(name: "search");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{train}/edit', 'edit')->name(name: 'edit');
        Route::get('/{train}/show', 'show')->name(name: 'show');
        Route::delete('/{train}/delete', 'delete')->name(name: 'delete');
    });
});
