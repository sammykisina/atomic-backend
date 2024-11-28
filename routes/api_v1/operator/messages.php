<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\Messages\IndexController;
use App\Http\Controllers\Domains\Operator\Messages\OperatorMakeAsReadController;
use App\Http\Controllers\Domains\Operator\Messages\OperatorUnreadMessagesController;
use App\Http\Controllers\Domains\Operator\Messages\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->prefix('messages')->as('messages:')->group(function (): void {
        Route::get('/', IndexController::class)->name(name: "index");
        Route::post('/', StoreController::class)->name(name: "store");
        Route::get('/unread', OperatorUnreadMessagesController::class)->name(name: "unread");
        Route::post('/driver/{driver}/mark-as-read', OperatorMakeAsReadController::class)->name(name: "mark-as-read");
    });
