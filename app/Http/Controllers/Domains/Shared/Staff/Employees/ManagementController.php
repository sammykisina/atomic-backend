<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Employees;

use Domains\Shared\Requests\Staff\EmployeeCreateOrEditRequest;

final class ManagementController
{
    // CREATE EMPLOYEE
    public function create(EmployeeCreateOrEditRequest $request): void
    {
        dd($request->validated());
    }
}
