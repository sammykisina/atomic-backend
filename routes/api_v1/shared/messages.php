<?php

declare(strict_types=1);


use App\Http\Controllers\Domains\Shared\Messages\IndexController;
use App\Http\Controllers\Domains\Shared\Messages\MakeAsReadController;
use App\Http\Controllers\Domains\Shared\Messages\StoreController;
use App\Http\Controllers\Domains\Shared\Messages\UnreadMessagesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: 'messages');
    Route::get('/unread', UnreadMessagesController::class)->name(name: 'unread-messages');
    Route::post('/', StoreController::class)->name(name: 'store');
    Route::post('/mark-as-read', MakeAsReadController::class)->name(name: "mark-as-read");
});
