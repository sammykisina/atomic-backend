<?php

declare(strict_types=1);

namespace Domains\Shared\Services\Staff;

use Domains\Shared\Models\Department;
use Illuminate\Support\Facades\Auth;

final class DepartmentService
{
    /**
     * CREATE DEPARTMENT
     * @param array $departmentData
     * @return Department
     */
    public function createDepartment(array $departmentData): Department
    {
        return Department::query()->create([
            'name' => $departmentData['name'],
            'description' => $departmentData['description'],
            'creator_id' => Auth::id(),
        ]);
    }

    /**
     * EDIT DEPARTMENT
     * @param Department $department
     * @param array $updatedDepartmentData
     * @return bool
     */
    public function editDepartment(Department $department, array $updatedDepartmentData): bool
    {
        return $department->update([
            'name' => $updatedDepartmentData['name'],
            'description' => $updatedDepartmentData['description'],
            'updater_id' => Auth::id(),
        ]);
    }
}
