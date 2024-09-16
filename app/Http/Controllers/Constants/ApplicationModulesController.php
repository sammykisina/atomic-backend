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
            'regions' => [
                'read-regions',
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
            'licenses' => [
                'read-licenses',
                'request-licenses',
                'edit-licenses',
                'delete-licenses',
                'confirm-licenses',
                'reject-licenses',
            ],
            'journeys' => [
                'read-journeys',
                'request-journeys',
                'edit-journeys',
                'delete-journeys',
            ],
            'journeys management' => [
                'read-journeys',
                'edit-journeys',
                'delete-journeys',
                'reject-journeys',
                'accept-journeys',
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
