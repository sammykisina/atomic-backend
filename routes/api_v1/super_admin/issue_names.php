<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\SuperAdmin\IssueNames\IndexController;
use App\Http\Controllers\Domains\SuperAdmin\IssueNames\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:read-issue-names,create-issue-names,edit-issue-names,delete-issue-names'])->prefix('issue-names')->as('issue-names:')->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{issueName}/edit', 'edit')->name(name: 'edit');
        Route::get('/{issueName}/show', 'show')->name(name: 'show');
    });
});
