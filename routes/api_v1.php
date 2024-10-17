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

        base_path('routes/api_v1/super_admin/locomotive_numbers.php'),

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

// PRINCIPLE_ENGINEER (CHIEF_CIVIL_ENGINEER)
Route::prefix('/principle-engineer')->as('principle-engineer:')->group(
    [
        base_path('routes/api_v1/principle_engineer/se_management.php'),
        base_path('routes/api_v1/principle_engineer/pe.php'),
    ],
);

// SENIOR ENGINEER  (REGIONAL_CIVIL_ENGINEER)
Route::prefix('/senior-engineer')->as('senior-engineer:')->group(
    [
        base_path('routes/api_v1/senior_engineer/rsti_management.php'),
        base_path('routes/api_v1/senior_engineer/se.php'),
    ],
);

// REGIONAL_SENIOR_TRACT_INSPECTOR
Route::prefix('/regional-senior-track-inspector')->as('regional-senior-track-inspector:')->group(
    [
        base_path('routes/api_v1/regional_senior_track_inspector/sti_management.php'),
        base_path('routes/api_v1/regional_senior_track_inspector/rsti.php'),
    ],
);

// PERMANENT_WAY_INSPECTOR
Route::prefix('/senior-track-inspector')->as('senior-track-inspector:')->group(
    [
        base_path('routes/api_v1/senior_track_inspector/inspection_schedules.php'),
        base_path('routes/api_v1/senior_track_inspector/inspections.php'),
        base_path('routes/api_v1/senior_track_inspector/issues.php'),
        base_path('routes/api_v1/senior_track_inspector/sti.php'),
    ],
);

// TRACK ATTENDANT
Route::prefix('/track-attendant')->as('track-attendant:')->group(
    [
        // INSPECTION SCHEDULES
        base_path('routes/api_v1/track_attendant/inspection.php'),
    ],
);

// SENIOR TRACK ATTENDANT
Route::prefix('/senior-track-attendant')->as('senior-track-attendant:')->group(
    [
        // ISSUES MANAGEMENT
        base_path('routes/api_v1/senior_track_attendant/issues.php'),
    ],
);
