<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\RegionalPermanentWayInspector\ShowMyRPWIAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-pwi-assignment'])->group(function (): void {
    Route::get('/assignments', ShowMyRPWIAssignmentController::class)->name(name: "show-rpwi-assignments");
});
