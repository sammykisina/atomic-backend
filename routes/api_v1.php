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

// SUPER ADMIN ROUTES
Route::prefix('/super-admin')->as('super-admin:')->group(
    [
        // COUNTY ROUTES
        base_path('routes/api_v1/super_admin/setup/county.php'),

        // DESK ROUTES
        base_path('routes/api_v1/super_admin/setup/desks.php'),

        // LINES
        base_path('routes/api_v1/super_admin/lines.php'),

        // STATIONS
        base_path('routes/api_v1/super_admin/stations.php'),

        // LOOPS
        base_path('routes/api_v1/super_admin/loops.php'),

        // SECTIONS
        base_path('routes/api_v1/super_admin/sections.php'),

        // SHIFTS
        base_path('routes/api_v1/super_admin/shifts.php'),

        // REGIONS MANAGEMENT
        base_path('routes/api_v1/super_admin/setup/regions.php'),
        base_path('routes/api_v1/super_admin/setup/line_regions_divisions.php'),
    ],
);

// DRIVER ROUTES
Route::prefix('/driver')->as('driver:')->group(
    [
        // JOURNEYS
        base_path('routes/api_v1/driver/journeys.php'),
    ],
);

// OPERATOR ROUTES
Route::prefix('/operator')->as('operator:')->group(
    [
        // JOURNEYS
        base_path('routes/api_v1/operator/journeys.php'),

        // SHIFTS
        base_path('routes/api_v1/operator/shifts.php'),
    ],
);

// NOTIFICATIONS
Route::prefix('/notifications')->as('notifications:')->group(
    base_path('routes/api_v1/shared/notifications.php'),
);

// REGINAL_CIVIL_ENGINEER
Route::prefix('/reginal-civil-engineer')->as('reginal-civil-engineer:')->group(
    [
        // INSPECTIONS
        base_path('routes/api_v1/reginal_civil_engineer/inspection_schedules.php'),
    ],
);

// INSPECTOR
Route::prefix('/inspector')->as('inspector:')->group(
    [
        // INSPECTION SCHEDULES
        base_path('routes/api_v1/inspector/inspection-schedules.php'),
    ],
);
