<?php

declare(strict_types=1);

namespace App\Http\Controllers\Constants;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ApplicationModulesController
{
    public function __invoke(Request $request): Response
    {
        $application_modules =  [
            'staff' => [
                'read-staff',
                'write-staff',
                'edit-staff',
                'delete-staff',
            ],
            'roles' => [
                'read-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'assign-roles',
                'revoke-roles',
                'create-permissions',
                'revoke-permissions',
                'revoke-ability',
            ],
            'departments' => [
                'read-departments',
                'create-departments',
                'edit-departments',
                'delete-departments',
            ],
            'lines' => [
                'read-lines',
                'create-lines',
                'edit-lines',
                'delete-lines',
            ],
            'loops' => [
                'read-loops',
                'create-loops',
                'edit-loops',
                'delete-loops',
            ],
            'sections' => [
                'read-sections',
                'create-sections',
                'edit-sections',
                'delete-sections',
            ],
            'stations' => [
                'read-stations',
                'create-stations',
                'edit-stations',
                'delete-stations',
            ],
            'counties' => [
                'read-counties',
            ],
            // PE
            'SE-management' => [
                'read-se-assignments',
                'create-se-assignments',
                'edit-se-assignments',
                'delete-se-assignments',
            ],
            'SE-inspections' => [
                'read-se-inspections',
                'show-se-inspections',
            ],

            // SE
            'RSTI-management' => [
                'read-rsti-assignments',
                'create-rsti-assignments',
                'edit-rsti-assignments',
                'delete-rsti-assignments',
            ],
            'RSTI-issues' => [
                'read-rsti-issues',
                'approve-speed-restriction',
            ],
            'RSTI-inspections' => [
                'read-rsti-inspections',
                'show-rsti-inspections',
            ],

            // RSTI
            'STI-management' => [
                'read-sti-assignments',
                'create-sti-assignments',
                'edit-sti-assignments',
                'delete-sti-assignments',
            ],

            'STI-inspections' => [
                'read-sti-inspections',
                'show-sti-inspections',
            ],

            // STI
            'inspections-management' => [
                'read-inspection-schedules',
                'create-inspection-schedules',
                'edit-inspection-schedules',
                'delete-inspection-schedules',
            ],


            'issues' => [
                'read-issues',
                'assign-issues',
            ],


            'journeys' => [
                'read-journeys',
                'request-journeys',
                'edit-journeys',
                'delete-journeys',
            ],
            'licenses' => [
                'read-licenses',
                'request-licenses',
                'edit-licenses',
                'delete-licenses',
                'confirm-licenses',
                'reject-licenses',
            ],
            'journeys-management' => [
                'read-journeys',
                'edit-journeys',
                'delete-journeys',
                'reject-journeys',
                'accept-journeys',
            ],
            'desks' => [
                'read-desks',
                'edit-desks',
                'create-desks',
                'delete-desks',
            ],
            'shifts' => [
                'read-shifts',
                'edit-shifts',
                'create-shifts',
                'delete-shifts',
                'reject-shifts',
                'accept-shifts',
                'manual-shift-activation',
            ],
            'regions' => [
                'read-regions',
                'create-regions',
                'edit-regions',
                'delete-regions',
                'line-regions-divisions',
            ],

            'TA-inspections' => [
                'read-inspections',
                'create-inspections',
                'confirm-inspection-schedules',
            ],
            'assignments' => [
                'read-assignments',
                'resolve-assignment',
            ],
            'issues-history' => [
                'read-issues-history',
                'delete-issues-history',
            ],



        ];

        return response(
            content: [
                'application_modules' => $application_modules,
                'message' => 'Application Modules',
            ],
            status: Http::OK(),
        );
    }
}
