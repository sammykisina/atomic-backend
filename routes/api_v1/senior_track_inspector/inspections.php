<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SeniorTrackInspector\Inspections\IndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-inspections'])->prefix('inspections')->as('inspections:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");
});
