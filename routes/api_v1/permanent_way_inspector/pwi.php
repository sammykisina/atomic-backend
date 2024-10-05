<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\PermanentWayInspector\ShowMyPWIAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-pwi-assignment'])->group(function (): void {
    Route::get('/assignments', ShowMyPWIAssignmentController::class)->name(name: "show-pwi-assignments");
});
