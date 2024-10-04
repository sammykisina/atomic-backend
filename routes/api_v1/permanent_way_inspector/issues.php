<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\PermanentWayInspector\Issues\IndexController;
use App\Http\Controllers\Domains\PermanentWayInspector\Issues\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-inspections'])->prefix('issues')->as('issues:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('{issue}/assignments', 'assignIssue')->name(name: 'assignIssue');
        Route::patch('/{assignment}/remove', 'removeAssignment')->name(name: 'delete');
        Route::patch('/{issue}/accept/resolution', 'acceptResolution')->name(name: 'accept-resolution');
        Route::patch('/{issue}/reject/resolution', 'rejectResolution')->name(name: 'reject-resolution');
    });
});
