<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Employees\IndexController;
use App\Http\Controllers\Domains\Shared\Staff\Employees\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:get-staff,create-staff,edit-staff,delete-staff'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name:'create');
    });
});
