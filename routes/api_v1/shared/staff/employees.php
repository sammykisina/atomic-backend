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


        Route::get('/download/employees-spreadsheet', 'exportEmployees')->name(name: 'download-employees-spreadsheet');
        Route::post('/upload/', 'importEmployees')->name(name: 'upload-employees');
    });
});
