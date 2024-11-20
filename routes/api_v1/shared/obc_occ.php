<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\OccAndObc\NotifyDriverAndOperatorOfOverSpeedingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::post('/journeys/{journey}/over-speeding', NotifyDriverAndOperatorOfOverSpeedingController::class)->name(name: 'over-speeding-notification');
});
