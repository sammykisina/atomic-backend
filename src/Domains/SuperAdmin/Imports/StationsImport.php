<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Imports;

use Domains\SuperAdmin\Enums\StationPositions;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class StationsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;
    use SkipsErrors;

    public function __construct(
        protected StationService $stationService,
        protected int $line_id,
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {

            $this->stationService->createStation(
                stationData: [
                    'name' => $row['name'],
                    'start_kilometer' => $row['start_kilometer'],
                    'end_kilometer' => $row['end_kilometer'],

                    'start_latitude_top' => $row['start_latitude_top'],
                    'start_longitude_top' => $row['start_longitude_top'],
                    'start_latitude_bottom' => $row['start_latitude_bottom'],
                    'start_longitude_bottom' => $row['start_longitude_bottom'],

                    'end_latitude_top' => $row['end_latitude_top'],
                    'end_longitude_top' => $row['end_longitude_top'],
                    'end_latitude_bottom' => $row['end_latitude_bottom'],
                    'end_longitude_bottom' => $row['end_longitude_bottom'],

                    'line_id' => $this->line_id,
                    'is_yard' => (bool) $row['is_yard'],
                    'position_from_line' => $row['position_from_line'],
                    'speed' => $row['speed'],
                ],
            );
        }
    }

    /**  @return array */
    public function rules(): array
    {
        return [
            '*.name' => [
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

            '*.start_latitude_top' => [
                'numeric',
                'required',
            ] ,
            '*.start_longitude_top' => [
                'numeric',
                'required',
            ],
            '*.start_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            '*.start_longitude_bottom' => [
                'numeric',
                'required',
            ],
            'end_latitude_top' => [
                'numeric',
                'required',
            ] ,
            '*.end_longitude_top' => [
                'numeric',
                'required',
            ],
            '*.end_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            '*.end_longitude_bottom' => [
                'numeric',
                'required',
            ],
            '*.is_yard' => [
                'required',
                'boolean',
            ],
            '*.position_from_line' => [
                'required',
                Rule::enum(StationPositions::class),
            ],
        ];
    }
}
