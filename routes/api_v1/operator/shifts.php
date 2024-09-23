<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Operator\Shifts\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-shifts,reject-shifts,accept-shifts'])
    ->prefix('shifts')->as('shifts:')->group(function (): void {
        Route::controller(ManagementController::class)->group(function (): void {
            Route::patch('{shift}/notifications/{notification}/accept-shift', 'acceptShift')->name(name: 'accept-shift');
        });
    });
