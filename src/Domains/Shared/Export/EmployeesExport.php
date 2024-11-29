<?php

declare(strict_types=1);

namespace Domains\Shared\Export;

use Domains\Shared\Models\Department;
use Domains\Shared\Models\Role;
use Domains\SuperAdmin\Models\Region; // Assuming you have a Role model
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

final class EmployeesExport implements Responsable, ShouldAutoSize, WithEvents, WithHeadings
{
    use Exportable;

    /**  @return array<int, string> */
    public function headings(): array
    {
        return [
            'first name',
            'last name',
            'email',
            'phone',
            'employee id',
            'national id',
            'type',          // Dropdown for type
            'department',    // Dropdown for department
            'region',        // Dropdown for region
            'work status',   // Dropdown for work status
            'role',          // Dropdown for role
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                // Apply bold styling to the first row (headings)
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Set column widths
                $columnWidths = [
                    'A' => 30, // first name
                    'B' => 30, // last name
                    'C' => 30, // email
                    'D' => 30, // phone
                    'E' => 30, // employee id
                    'F' => 30, // national id
                    'G' => 30, // type
                    'H' => 30, // department
                    'I' => 30, // region
                    'J' => 30, // work status
                    'K' => 30, // role
                ];

                foreach ($columnWidths as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                // Fetch departments, regions, and roles from the database
                $departments = Department::query()->get();
                $regions = Region::query()->get();
                $roles = Role::has('permissions')->get();

                // Prepare dropdown options for each (format: "id - name")
                $departmentOptions = $departments->map(fn($department) => "{$department->id} - {$department->name}")->toArray();

                $regionOptions = $regions->map(fn($region) => "{$region->id} - {$region->name}")->toArray();

                $roleOptions = $roles->map(fn($role) => "{$role->id} - {$role->name}")->toArray();

                // Generate options for type from the defined constants
                $typeOptions = [
                    'SYSTEM_ADMIN',
                    'SUPER_ADMIN',
                    'PRINCIPLE_ENGINEER',
                    'SENIOR_ENGINEER',
                    'REGIONAL_SENIOR_TRACK_INSPECTOR',
                    'SENIOR_TRACK_INSPECTOR',
                    'TRACK_INSPECTOR',
                    'SENIOR_TRACK_ATTENDANT',
                    'TRACK_ATTENDANT',
                    'OPERATOR_CONTROLLER',
                    'DRIVER',
                ];

                // Generate options for work status from the defined constants
                $workStatusOptions = [
                    'ON_THE_JOB',
                    'ON_LEAVE',
                ];

                // Define the columns where the dropdowns will be added
                $typeColumn = 'G';       // Type column
                $departmentColumn = 'H'; // Department column
                $regionColumn = 'I';     // Region column
                $workStatusColumn = 'J'; // Work status column
                $roleColumn = 'K';       // Role column

                // Define the number of rows for the dropdowns
                $rowCount = 100; // Adjust based on your expected row count

                // Create dropdown validation for each column
                for ($row = 2; $row <= $rowCount; $row++) {
                    // Type dropdown
                    $this->applyDropdownValidation($event, $typeColumn, $row, $typeOptions);

                    // Department dropdown
                    $this->applyDropdownValidation($event, $departmentColumn, $row, $departmentOptions);

                    // Region dropdown
                    $this->applyDropdownValidation($event, $regionColumn, $row, $regionOptions);

                    // Work status dropdown
                    $this->applyDropdownValidation($event, $workStatusColumn, $row, $workStatusOptions);

                    // Role dropdown
                    $this->applyDropdownValidation($event, $roleColumn, $row, $roleOptions);
                }
            },
        ];
    }


    /**
     * Apply dropdown validation to a specific cell in the sheet.
     *
     * @param AfterSheet $event
     * @param string $column
     * @param int $row
     * @param array $options
     * @return void
     */
    private function applyDropdownValidation(AfterSheet $event, string $column, int $row, array $options): void
    {
        $validation = $event->sheet->getDelegate()->getCell("{$column}{$row}")->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1(sprintf('"%s"', implode(',', $options)));
    }
}
