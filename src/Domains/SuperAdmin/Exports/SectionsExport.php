<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Exports;

use Domains\SuperAdmin\Services\LineService;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

final class SectionsExport implements Responsable, ShouldAutoSize, WithEvents, WithHeadings
{
    use Exportable;

    public function __construct(
        protected int $line_id,
    ) {}

    /**  @return array<int, string> */
    public function headings(): array
    {
        return [
            'start name',
            'end name',
            'start kilometer',
            'end kilometer',
            'start latitude',
            'start longitude',
            'end latitude',
            'end longitude',
            'station',
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
                $event->sheet->getStyle('A1:J1')->applyFromArray([
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
                ];

                foreach ($columnWidths as $column => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                // Fetch stations based on the line_id
                $line = LineService::getLineWithId(line_id: $this->line_id);
                // Create options for stations dropdown in 'station' column (id - name)
                $station_id_name_options = $line->stations->map(fn($station) => "{$station->id} - {$station->name}")->toArray();

                // Create options for start name and end name (just station names)
                $station_name_options = $line->stations->map(fn($station) => $station->name)->toArray();

                $row_count = 100;

                for ($row = 2; $row <= $row_count; $row++) {
                    // Apply dropdown validation for start and end names (station names)
                    $this->applyDropdownValidation($event, 'A', $row, $station_name_options); // Start name
                    $this->applyDropdownValidation($event, 'B', $row, $station_name_options); // End name

                    // Apply dropdown validation for station column (id - name)
                    $this->applyDropdownValidation($event, 'I', $row, $station_id_name_options); // Station column
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
