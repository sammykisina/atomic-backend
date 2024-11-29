<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SeniorTrackAttendant\IssueManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:resolve-assignments'])->prefix('issues')->as('issues:')->group(function (): void {
    Route::controller(IssueManagementController::class)->group(function (): void {
        Route::post('/{issue}/assignments/{assignment}/resolve', 'resolve')->name(name: 'resolve-issue');
        Route::get('/history', 'index')->name(name: 'index');
        Route::post('issue-area/{issueArea}/mark-area-under-maintenance', 'markAreaUnderMaintenance')->name(name: 'mark-area-under-maintenance');
    });
});
