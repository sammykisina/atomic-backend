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
            'inspections-management' => [
                'read-inspection-schedules',
                'create-inspection-schedules',
                'edit-inspection-schedules',
                'delete-inspection-schedules',
            ],
            'inspections' => [
                'read-inspections',
                'create-inspections',
                'confirm-inspection-schedules',
            ],
            'RCE-management' => [
                'read-rce-assignment',
                'create-rce-assignment',
                'edit-rce-assignment',
                'delete-rce-assignment',
            ],
            'RPWI-management' => [
                'read-rpwi-assignment',
                'create-rpwi-assignment',
                'edit-rpwi-assignment',
                'delete-rpwi-assignment',
            ],
            'assignments' => [
                'read-assignments',
                'resolve-assignment',
            ],
            'issues-history' => [
                'read-issues-history',
                'delete-issues-history',
            ],
            'PWI-inspections' => [
                'read-pwi-inspections',
                'show-pwi-inspections',
            ],
            'PWI-management' => [
                'read-pwi-assignment',
                'create-pwi-assignment',
                'edit-pwi-assignment',
                'delete-pwi-assignment',
            ],
            'RPWI-inspections' => [
                'read-rpwi-inspections',
                'show-rpwi-inspections',
            ],
            'RCE-inspections' => [
                'read-rce-inspections',
                'show-rce-inspections',
            ],
            'issues' => [
                'read-issues',
                'assign-issues',
            ],
            'RPWI-issues' => [
                'read-rpwi-issues',
                'approve-speed-restriction',
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
