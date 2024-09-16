<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::controller(NotificationController::class)->group(function (): void {
        Route::get('{user}/unread', 'unreadNotifications')->name(name: 'unread-notifications');
        Route::get('{user}/read', 'readNotifications')->name(name: 'read-notifications');
    });
});
