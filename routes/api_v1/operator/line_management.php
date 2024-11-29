<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\LineManagement;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-journeys'])->group(function (): void {
    Route::controller(LineManagement::class) ->prefix('lines')->as('lines:')->group(function (): void {
        Route::post('add-interdiction', 'addInterdiction')->name(name: 'add-interdiction');
        Route::post('remove-interdiction', 'removeInterdiction')->name(name: 'remove-interdiction');
    });
});
