<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// EMPLOYEE MANAGEMENT
Route::prefix('/employees')->as('employees:')->group(
    base_path('routes/api_v1/shared/staff/employees.php'),
);

// REGIONS
Route::prefix('/regions')->as('regions:')->group(
    base_path('routes/api_v1/shared/staff/regions.php'),
);
