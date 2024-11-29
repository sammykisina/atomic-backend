<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Setup\Counties\IndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-counties'])->prefix('counties')->as('counties:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");
});
