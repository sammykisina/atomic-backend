<?php

declare(strict_types=1);

namespace Domains\Shared\Services\Staff;

use Domains\Shared\Models\Desk;
use Domains\Shared\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class EmployeeService
{
    /**
     * CREATE EMPLOYEE
     * @param array $employeeData
     * @return User
     */
    public function createEmployee(array $employeeData): User
    {
        return User::query()->create([
            'first_name' => $employeeData['first_name'],
            'last_name' => $employeeData['last_name'],
            'email' => $employeeData['email'],
            'phone' => $employeeData['phone'],
            'employee_id' => $employeeData['employee_id'],
            'national_id' => $employeeData['national_id'],
            'type' => $employeeData['type'],
            'department_id' => $employeeData['department_id'],
            'region_id' => $employeeData['region_id'],
            'work_status' => $employeeData['work_status'],
            'creator_id' => Auth::id(),
            'password' => Hash::make(
                value: $employeeData['email'],
            ),
        ]);
    }


    /**
     * UPDATE EMPLOYEE
     * @param User $employee
     * @param array $employeeUpdatedData
     * @return bool
     */
    public function editEmployee(User $employee, array $updatedEmployeeData): bool
    {
        return $employee->update([
            'first_name' => $updatedEmployeeData['first_name'],
            'last_name' => $updatedEmployeeData['last_name'],
            'email' => $updatedEmployeeData['email'],
            'phone' => $updatedEmployeeData['phone'],
            'employee_id' => $updatedEmployeeData['employee_id'],
            'national_id' => $updatedEmployeeData['national_id'],
            'type' => $updatedEmployeeData['type'],
            'department_id' => $updatedEmployeeData['department_id'],
            'region_id' => $updatedEmployeeData['region_id'],
            'work_status' => $updatedEmployeeData['work_status'],
            'updater_id' => Auth::id(),
        ]);
    }

    /**
     * UPDATE EMPLOYEE CURRENT DESK
     * @param User $employee
     * @param int $desk_id
     * @return bool
     */
    public function updateEmployeeDesk(User $employee, int $desk_id): bool
    {
        $employee->desks()->attach($desk_id, ['status' => true]);

        return $employee->update([
            'active_desk_id' => $desk_id,
        ]);
    }

    /**
     * MOVE EMPLOYEE TO NEW DESK
     * @param User $employee
     * @param Desk $desk
     * @return bool
     */
    public function moveEmployeeToNewDesk(User $employee, Desk $desk): bool
    {
        $result = DB::transaction(function () use ($employee, $desk) {
            $employee->desks()->updateExistingPivot($desk->id, [
                'status' => false,
            ]);

            $employee->desks()->attach($desk->id, ['status' => true]);

            return $employee->update([
                'active_desk_id' => $desk->id,
            ]);
        });

        return $result;
    }
}
