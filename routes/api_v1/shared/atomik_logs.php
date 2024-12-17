<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\AtomikLogs\GenerateLogsReportController;
use App\Http\Controllers\Domains\Shared\AtomikLogs\IndexController;
use App\Http\Controllers\Domains\Shared\AtomikLogs\ShowAtomikLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: 'atomik_logs');
    Route::get('{atomikLog}/show', ShowAtomikLogController::class)->name(name: 'show');
    Route::get('{train}/generate/logs', GenerateLogsReportController::class)->name(name: 'generate-logs');
});
