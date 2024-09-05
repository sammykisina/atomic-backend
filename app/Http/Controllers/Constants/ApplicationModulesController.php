<?php

declare(strict_types=1);

namespace App\Http\Controllers\Constants;

use Illuminate\Http\Request;

final class ApplicationModulesController
{
    public function __invoke(Request $request)
    {
        return [
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
    }
}
