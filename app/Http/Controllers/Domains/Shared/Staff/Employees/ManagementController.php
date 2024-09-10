<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Employees;

use Domains\Shared\Models\User;
use Domains\Shared\Requests\Staff\CreateOrEditEmployeeRequest;
use Domains\Shared\Resources\UserResource;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected EmployeeService $employeeService,
    ) {}

    /**
     * CREATE EMPLOYEE
     * @param CreateOrEditEmployeeRequest $request
     * @return Response | HttpException
     */
    public function create(CreateOrEditEmployeeRequest $request): HttpException | Response
    {
        $employee = $this->employeeService->createEmployee(
            employeeData: $request->validated(),
        );

        if ( ! $employee) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Employee creation failed.',
            );
        }

        return response(
            content: [
                'employee' => new UserResource(
                    resource: $employee,
                ),
                'message' => 'Employee created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT EMPLOYEE
     * @param CreateOrEditEmployeeRequest $request
     * @param User $employee
     * @return Response | HttpException
     */
    public function edit(CreateOrEditEmployeeRequest $request, User $employee): Response | HttpException
    {
        if( ! $this->employeeService->editEmployee(
            employee: $employee,
            updatedEmployeeData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Employee update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Employee updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
