<?php

declare(strict_types=1);

namespace Domains\Shared\Import;

use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class EmployeesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;
    use SkipsErrors;

    public function __construct(
        protected EmployeeService $employeeService,
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            list($role_id) = explode(' - ', $row['role']);
            list($department_id) = explode(' - ', $row['department']);
            list($region_id) = explode(' - ', $row['region']);

            $this->employeeService->createEmployee(
                employeeData: [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'employee_id' => $row['employee_id'],
                    'national_id' => $row['national_id'],
                    'type' => $row['type'],
                    'department_id' => (int) $department_id,
                    'region_id' => (int) $region_id,
                    'work_status' => $row['work_status'],
                    'role_id' => (int) $role_id,
                ],
            );
        }
    }

    /**  @return array */
    public function rules(): array
    {
        return [
            '*.first_name' => [
                'required',
                'string',
            ],
            '*.last_name' => [
                'required',
                'string',
            ],
            '*.email' => [
                'email',
                'unique:users,email',
            ],
            '*.phone' => [
                'required',
                'unique:users,phone',
            ],
            '*.employee_id' => [
                'required',
                'unique:users,employee_id',
            ],
            '*.national_id' => [
                'required',
                'unique:users,national_id',
            ],
            '*.type' => [
                'required',
                Rule::enum(UserTypes::class),
            ],
            '*.department' => [
                'required',
            ],
            '*.region' => [
                'required',
            ],
            '*.work_status' => [
                'required',
                Rule::enum(WorkStatuses::class),
            ],
            '*.role' => [
                'required',
            ],
        ];
    }
}
