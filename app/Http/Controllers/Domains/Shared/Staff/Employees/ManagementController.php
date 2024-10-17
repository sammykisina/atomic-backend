<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Employees;

use Domains\Shared\Export\EmployeesExport;
use Domains\Shared\Import\EmployeesImport;
use Domains\Shared\Models\User;
use Domains\Shared\Requests\Staff\CreateOrEditEmployeeRequest;
use Domains\Shared\Resources\UserResource;
use Domains\Shared\Services\Staff\EmployeeService;
use Domains\Shared\Services\Staff\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
            $role = RoleService::getRole(
                role_id: $request->validated(
                    key: 'role_id',
                ),
            );

            if (0 === $role->permissions->count()) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This role has no permissions yet.Please add them before you assign it to someone.',
                );
            }

            $employee = $this->employeeService->createEmployee(
                employeeData: array_merge(
                    [
                        'region_id' => $request->validated('region_id') ?? null,
                    ],
                    $request->validated(),
                ),
            );

            if ( ! $employee) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Employee creation failed.',
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
     * EMPLOYEE SHOW
     * @param User $employee
     * @return Response
     */
    public function show(User $employee): Response
    {
        return response(
            content: [
                'message' => 'Employee fetched successfully.',
                'employee' => new UserResource(
                    resource: $employee,
                ),
            ],
            status: Http::OK(),
        );
    }


    /**
     * @param Excel $excel
     * @return BinaryFileResponse
     */
    public function exportEmployees(Excel $excel): BinaryFileResponse
    {
        return $excel->download(export: new EmployeesExport(), fileName: 'employees.xlsx');
    }

    /**
     * @param Request $request
     */
    public function importEmployees(Request $request)
    {
        $import = new EmployeesImport(
            employeeService: $this->employeeService,
        );

        $import->import($request->file('employees'));

        if ($import->errors()->isNotEmpty()) {
            return response(
                content: [
                    'message' => 'Please check the following issues in your spread sheet',
                    'errors' => $import->errors(),
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'Employees uploaded successfully',
            ],
            status: Http::CREATED(),
        );
    }
}
