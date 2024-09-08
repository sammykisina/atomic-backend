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
