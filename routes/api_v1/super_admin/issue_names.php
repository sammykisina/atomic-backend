<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\IssueNames\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\IssueNames\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-issue_names,create-issue_names,edit-issue_names,delete-issue_names'])->prefix('issue_names')->as('issue_names:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{issueName}/edit', 'edit')->name(name: 'edit');
        Route::get('/{issueName}/show', 'show')->name(name: 'show');
    });
});
