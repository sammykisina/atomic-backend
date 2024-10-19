<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

final class StationsExport implements Responsable, ShouldAutoSize, WithEvents, WithHeadings
{
    use Exportable;

    /**  @return array<int, string> */
    public function headings(): array
    {
        return [
            'name',
            'start kilometer',
            'end kilometer',
            'start latitude top',
            'start longitude top',
            'start latitude bottom',
            'start longitude bottom',
            'end latitude top',
            'end longitude top',
            'end latitude bottom',
            'end longitude bottom',
            'is yard',
            'position from line',
            'speed',
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
                $event->sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Set column widths
                $columnWidths = [
                    'A' => 30,
                    'B' => 30,
                    'C' => 30,
                    'D' => 30,
                    'E' => 30,
                    'F' => 30,
                    'G' => 30,
                    'H' => 30,
                    'I' => 30,
                    'J' => 30,
                    'K' => 30,
                    'L' => 30,
                    'M' => 30,
                    'N' => 30,
                ];

                foreach ($columnWidths as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                $station_position_options = [
                    'left',
                    'right',
                ];

                $is_yard_options = [
                    'true',
                    'false',
                ];

                $is_yard_column = 'L';
                $station_position_column = 'M';

                $row_count = 100;

                for ($row = 2; $row <= $row_count; $row++) {
                    $this->applyDropdownValidation($event, $station_position_column, $row, $station_position_options);
                    $this->applyDropdownValidation($event, $is_yard_column, $row, $is_yard_options);
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
