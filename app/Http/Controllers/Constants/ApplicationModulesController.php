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
                'read_staff',
                'write_staff',
                'update_staff',
                'delete_staff',
            ],
            'roles' => [
                'read_role',
                'create_role',
                'update_role',
                'delete_role',
                'assign_role',
                'revoke_role',
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
