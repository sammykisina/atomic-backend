<?php

declare(strict_types=1);

;
use App\Http\Controllers\Domains\Operator\Shifts\AcknowledgePanicController;
use App\Http\Controllers\Domains\Operator\Shifts\IndexController;
use App\Http\Controllers\Domains\Operator\Shifts\ManagementController;
use App\Http\Controllers\Domains\Operator\Shifts\ShiftPanicsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-shifts,reject-shifts,accept-shifts,manual-shift-activation'])
    ->prefix('shifts')->as('shifts:')->group(function (): void {
        Route::get('/show', IndexController::class)->name(name: 'shift');
        Route::get('/panics', ShiftPanicsController::class)->name(name: 'shift-panics');
        Route::patch('/panics/{panic}/acknowledge', AcknowledgePanicController::class)->name(name: 'acknowledge-panic');


        Route::controller(ManagementController::class)->group(function (): void {
            Route::patch('{shift}/notifications/{notification}/accept-shift', 'acceptShift')->name(name: 'accept-shift');
            Route::patch('{shift}/manual-shift-activation', 'manualShiftActivation')->name(name: 'manual-shift-activation');
        });
    });
