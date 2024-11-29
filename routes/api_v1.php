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


// MESSAGES
Route::prefix('/messages')->as('messages:')->group(
    base_path('routes/api_v1/shared/messages.php'),
);


// ATOMIC LOGS
Route::prefix('/atomic-logs')->as('atomik-logs:')->group(
    base_path('routes/api_v1/shared/atomik_logs.php'),
);

// OBC- OCC
Route::prefix('/obc-occ')->as('obc-occ:')->group(
    base_path('routes/api_v1/shared/obc_occ.php'),
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


        // LINES
        base_path('routes/api_v1/super_admin/lines.php'),

        // LOCOMOTIVE NUMBERS
        base_path('routes/api_v1/super_admin/locomotive_numbers.php'),

        // STATIONS
        base_path('routes/api_v1/super_admin/stations.php'),

        // LOOPS
        base_path('routes/api_v1/super_admin/loops.php'),

        // SECTIONS
        base_path('routes/api_v1/super_admin/sections.php'),


        // REGIONS MANAGEMENT
        base_path('routes/api_v1/super_admin/setup/regions.php'),
        base_path('routes/api_v1/super_admin/setup/line_regions_divisions.php'),

        // TRAIN NAMES
        base_path('routes/api_v1/super_admin/train_names.php'),

        // TRAINS
        base_path('routes/api_v1/super_admin/trains.php'),

        // GROUPS
        base_path('routes/api_v1/super_admin/shift_management/groups.php'),

        // DESK ROUTES
        base_path('routes/api_v1/super_admin/shift_management/desks.php'),

        // SHIFTS
        base_path('routes/api_v1/super_admin/shift_management/shifts.php'),

        // ISSUE NAMES
        base_path('routes/api_v1/super_admin/issue_names.php'),
    ],
);

// DRIVER ROUTES
Route::prefix('/driver')->as('driver:')->group(
    [
        // JOURNEYS
        base_path('routes/api_v1/driver/journeys.php'),

        // LICENSES
        base_path('routes/api_v1/driver/licenses.php'),

        // PANICS
        base_path('routes/api_v1/driver/panics.php'),
    ],
);

// OPERATOR ROUTES
Route::prefix('/operator')->as('operator:')->group(
    [
        // JOURNEYS
        base_path('routes/api_v1/operator/journeys.php'),

        // SHIFTS
        base_path('routes/api_v1/operator/shifts.php'),

        // LICENSES
        base_path('routes/api_v1/operator/licenses.php'),

        // MESSAGES
        base_path('routes/api_v1/operator/messages.php'),


        // LINE MANAGEMENT
        base_path('routes/api_v1/operator/line_management.php'),
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
        base_path('routes/api_v1/senior_engineer/speeds.php'),
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
