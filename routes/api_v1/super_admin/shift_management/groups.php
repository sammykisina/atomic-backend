<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\ShiftManagement\Groups\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\ShiftManagement\Groups\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-groups,create-groups,edit-groups,delete-groups'])->prefix('groups')->as('groups:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::get('/{group}/show', 'show')->name(name: 'show');
        Route::delete('/{group}/delete', 'delete')->name(name: 'delete');
        Route::patch('/{group}/edit', 'edit')->name(name: 'edit');
    });
});
