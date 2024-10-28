<?php

declare(strict_types=1);


use App\Http\Controllers\Domains\Shared\Messages\IndexController;
use App\Http\Controllers\Domains\Shared\Messages\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: 'messages');
    Route::post('/journeys/{journey}/store', StoreController::class)->name(name: 'store');
});
