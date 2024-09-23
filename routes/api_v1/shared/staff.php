<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// EMPLOYEE MANAGEMENT
Route::prefix('/employees')->as('employees:')->group(
    base_path('routes/api_v1/shared/staff/employees.php'),
);

// DEPARTMENTS
Route::prefix('/departments')->as('departments:')->group(
    base_path('routes/api_v1/shared/staff/departments.php'),
);

// ROLES
Route::prefix('/roles')->as('roles:')->group(
    base_path('routes/api_v1/shared/staff/roles.php'),
);
