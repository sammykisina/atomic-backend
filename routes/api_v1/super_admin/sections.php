<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\Sections\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\Sections\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-sections,create-sections,edit-sections,delete-sections'])->prefix('sections')->as('sections:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{section}/edit', 'edit')->name(name: 'edit');
        Route::get('/{section}/show', 'show')->name(name: 'show');
        Route::delete('/{section}/delete', 'delete')->name(name: 'delete');

        Route::get('/download/sections-spreadsheet', 'exportSections')->name(name: 'download-sections-spreadsheet');
        Route::post('/upload/', 'importSections')->name(name: 'upload-sections');
    });
});
