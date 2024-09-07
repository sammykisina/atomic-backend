<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// AUTH ROUTES
Route::prefix('/auth')->as('auth:')->group(
    base_path('routes/api_v1/shared/auth.php'),
);

// CONSTANTS
Route::prefix('/constants')->as('constants:')->group(
    base_path('routes/api_v1/constants.php'),
);

// STAFF
Route::prefix('/staff')->as('staff:')->group(
    base_path('routes/api_v1/shared/staff.php'),
);
