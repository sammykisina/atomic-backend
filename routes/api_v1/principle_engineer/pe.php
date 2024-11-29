<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\PrincipleEngineer\InspectionsIndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-se-inspections'])->group(function (): void {
    Route::get('/inspections', InspectionsIndexController::class)->name(name: "se-inspections");
});
