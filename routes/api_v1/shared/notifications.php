<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::controller(NotificationController::class)->group(function (): void {
        Route::get('/unread', 'unreadNotifications')->name(name: 'unread-notifications');
        Route::get('/read', 'readNotifications')->name(name: 'read-notifications');
        Route::patch('{notification}/mark-as-read', 'markAsRead')->name(name: 'mark-as-read');
    });
});
