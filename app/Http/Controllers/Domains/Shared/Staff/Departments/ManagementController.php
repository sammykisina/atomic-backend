<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Departments;

use Domains\Shared\Models\Department;
use Domains\Shared\Requests\Staff\CreateOrEditDepartmentRequest;
use Domains\Shared\Resources\DepartmentResource;
use Domains\Shared\Services\Staff\DepartmentService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected DepartmentService $departmentService,
    ) {}


    /**
     * CREATE DEPARTMENT
     * @param CreateOrEditDepartmentRequest $request
     * @return HttpException | Response
     */
    public function create(CreateOrEditDepartmentRequest $request): Response | HttpException
    {
        $department = $this->departmentService->createDepartment(
            departmentData: $request->validated(),
        );

        if( ! $department) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Department creation failed.',
            );
        }

        return response(
            content: [
                'department' => new DepartmentResource(
                    resource: $department,
                ),
                'message' => 'Department created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT DEPARTMENT
     * @param CreateOrEditDepartmentRequest $request
     * @param Department $department
     * @return Response | HttpException
     */
    public function edit(CreateOrEditDepartmentRequest $request, Department $department): Response | HttpException
    {
        if( ! $this->departmentService->editDepartment(
            updatedDepartmentData: $request->validated(),
            department: $department,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Department creation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Department updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
