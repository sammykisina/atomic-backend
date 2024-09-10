<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Loops\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:read-loops,create-loops,update-loops,delete-loops'])->group(function (): void {
    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('lines/{line}/stations/{station}/loops', 'create')->name(name:'create');
        Route::patch('lines/{line}/{station}/loops/{loop}/edit', 'edit')->name(name:'edit');
    });
});
