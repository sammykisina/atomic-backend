<?php

declare(strict_types=1);

use App\Http\Controllers\Constants\ApplicationModulesController;
use Illuminate\Support\Facades\Route;

// APPLICATION MODULES
// Route::middleware('auth:sanctum')->group(function (): void {
// });
Route::get('/application/modules', ApplicationModulesController::class)->name(name: "application-modules");
