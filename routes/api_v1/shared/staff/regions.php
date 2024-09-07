<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Regions\IndexController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:get-regions'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");
});
