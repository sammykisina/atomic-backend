<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SeniorTrackInspector\ShowMySTIAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-sti-assignments'])->group(function (): void {
    Route::get('/assignments', ShowMySTIAssignmentController::class)->name(name: "my-sti-assignments");
});
