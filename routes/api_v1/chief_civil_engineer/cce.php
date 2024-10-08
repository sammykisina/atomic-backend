<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\ChiefCivilEngineer\InspectionsIndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-cce-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "show-cce-assignments");
});
