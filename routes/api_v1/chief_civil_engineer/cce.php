<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\ChiefCivilEngineer\InspectionsIndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-rce-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "rce-inspections");
});
