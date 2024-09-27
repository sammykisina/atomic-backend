<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Departments\IndexController;
use App\Http\Controllers\Domains\Shared\Staff\Departments\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-departments,create-departments,edit-departments,delete-departments'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");


    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::get('{department}/show', 'show')->name(name: 'show');
        Route::patch('/{department}/edit', 'edit')->name(name: 'edit');
    });
});
