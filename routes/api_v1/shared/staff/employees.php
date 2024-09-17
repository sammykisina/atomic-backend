<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Employees\IndexController;
use App\Http\Controllers\Domains\Shared\Staff\Employees\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-staff,create-staff,edit-staff,delete-staff'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::get('{employee}/show', 'show')->name(name: 'show');
        Route::patch('/{employee}/edit', 'edit')->name(name: 'edit');
        Route::patch('/{employee}/desks/{desk}', 'moveEmployeeToDesk')->name(name: 'move-employee-to-desk');
        Route::patch('/{employee}/desks/{desk}/remove', 'removeEmployeeFromDesk')->name(name: 'remove-employee-from-desk');
    });
});
