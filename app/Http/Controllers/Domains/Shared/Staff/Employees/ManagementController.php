<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Employees;

use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\Desk;
use Domains\Shared\Models\User;
use Domains\Shared\Requests\Staff\CreateOrEditEmployeeRequest;
use Domains\Shared\Resources\UserResource;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

        $employee = DB::transaction(function () use ($request) {

            $employee = $this->employeeService->createEmployee(
                employeeData: $request->validated(),
            );

            if ( ! $employee) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Employee creation failed.',
                );
            }

            if ($request->validated(key : 'type') === UserTypes::OPERATOR_CONTROLLER->value) {
                $this->employeeService->updateEmployeeDesk(
                    employee: $employee,
                    desk_id: $request->validated(key : 'desk_id'),
                );
            }

            return $employee;
        });

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
        if ( ! $this->employeeService->editEmployee(
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


    /**
     * MOVE EMPLOYEE TO DESk
     * @param User $employee
     * @param Desk $desk
     * @return Response|HttpException
     */
    public function moveEmployeeToDesk(User $employee, Desk $desk): Response | HttpException
    {
        // dd($employee->type);
        if (UserTypes::OPERATOR_CONTROLLER !== $employee->type) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Employee  is not operator controller.',
            );
        }

        if ($employee->active_desk_id === $desk->id) {
            return response(
                content: [
                    'message' => 'Employee already assigned to this desk.',
                ],
                status: Http::FOUND(),
            );
        }

        if ( ! $this->employeeService->moveEmployeeToNewDesk(employee: $employee, desk: $desk)) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Employee not assigned to desk.',
            );

        }

        return response(
            content: [
                'message' => 'Employee assigned to desk successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
