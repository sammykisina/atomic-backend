<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Imports;

use Domains\SuperAdmin\Services\Sectionservice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class SectionsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;
    use SkipsErrors;

    public function __construct(
        protected Sectionservice $sectionService,
        protected int $line_id,
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            list($station_id) = explode(' - ', $row['station']);

            $this->sectionService->createSection(
                sectionData: [
                    'start_name' => $row['start_name'],
                    'end_name' => $row['end_name'],
                    'start_kilometer' => $row['start_kilometer'],
                    'end_kilometer' => $row['end_kilometer'],
                    'start_latitude' => $row['start_latitude'],
                    'start_longitude' => $row['start_longitude'],
                    'end_latitude' => $row['end_latitude'],
                    'end_longitude' => $row['end_longitude'],
                    'line_id' => $this->line_id,
                    'station_id' => (int) $station_id,
                    'number_of_kilometers_to_divide_section_to_subsection' => 5,
                    'speed' => $row['speed'],
                 ],
            );
        }
    }

    /**  @return array */
    public function rules(): array
    {
        return [
            '*.start_name' => [
                'required',
                'string',
            ],
            '*.end_name' => [
                'required',
                'string',
            ],
            '*.start_kilometer' => [
                'numeric',
                'required',
            ],
            '*.end_kilometer' => [
                'numeric',
                'required',
            ] ,

            '*.start_latitude' => [
                'numeric',
                'required',
            ] ,
            '*.start_longitude' => [
                'numeric',
                'required',
            ],
            '*.end_latitude' => [
                'numeric',
                'required',
            ] ,
            '*.end_longitude' => [
                'numeric',
                'required',
            ],
            'station' => [
                'required',
            ],
            'speed' => [
                'required',
            ],
        ];
    }
}
