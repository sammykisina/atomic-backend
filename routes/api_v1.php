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

// DASHBOARDS
Route::prefix('/dashboards')->as('dashboards:')->group(
    base_path('routes/api_v1/shared/dashboards.php'),
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

// CHIEF_CIVIL_ENGINEER
Route::prefix('/chief-civil-engineer')->as('chief-civil-engineer:')->group(
    [
        base_path('routes/api_v1/chief_civil_engineer/rce_management.php'),
        base_path('routes/api_v1/chief_civil_engineer/cce.php'),
    ],
);

// REGIONAL_CIVIL_ENGINEER
Route::prefix('/regional-civil-engineer')->as('regional-civil-engineer:')->group(
    [
        base_path('routes/api_v1/regional_civil_engineer/rpwi_management.php'),
        base_path('routes/api_v1/regional_civil_engineer/rce.php'),
    ],
);

// REGIONAL_PERMANENT_WAY_INSPECTOR
Route::prefix('/regional-permanent-way-inspector')->as('regional-permanent-way-inspector:')->group(
    [
        base_path('routes/api_v1/regional_permanent_way_inspector/pwi_management.php'),
        base_path('routes/api_v1/regional_permanent_way_inspector/rpwi.php'),
    ],
);

// PERMANENT_WAY_INSPECTOR
Route::prefix('/permanent-way-inspector')->as('permanent-way-inspector:')->group(
    [
        base_path('routes/api_v1/permanent_way_inspector/inspection_schedules.php'),
        base_path('routes/api_v1/permanent_way_inspector/inspections.php'),
        base_path('routes/api_v1/permanent_way_inspector/issues.php'),
        base_path('routes/api_v1/permanent_way_inspector/pwi.php'),
    ],
);

// INSPECTOR
Route::prefix('/inspector')->as('inspector:')->group(
    [
        // INSPECTION SCHEDULES
        base_path('routes/api_v1/inspector/inspection.php'),
    ],
);

// GANG MAN
Route::prefix('/gang-man')->as('gang-man:')->group(
    [
        // ISSUES MANAGEMENT
        base_path('routes/api_v1/gang_man/issues.php'),
    ],
);
