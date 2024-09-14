<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * SHARED ROUTES
 */

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

/**
 * SUPER ADMIN ROUTES
 */
Route::prefix('/super-admin')->as('super-admin:')->group(
    [
        // COUNTY ROUTES
        base_path('routes/api_v1/super_admin/setup/county.php'),

        // LINES
        base_path('routes/api_v1/super_admin/lines.php'),

        // STATIONS
        base_path('routes/api_v1/super_admin/stations.php'),

        // LOOPS
        base_path('routes/api_v1/super_admin/loops.php'),

        // SECTIONS
        base_path('routes/api_v1/super_admin/sections.php'),
    ],
);

/**
 * DRIVER ROUTES
 */
Route::prefix('/driver')->as('driver:')->group(
    [
        // JOURNEYS
        base_path('routes/api_v1/driver/journeys.php'),
    ],
);
