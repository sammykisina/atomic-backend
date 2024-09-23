<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class LineRegionDivideRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'region_divisions' => [
                'required',
                'array',
            ],
            'region_divisions.*.region_id' => [
                'required',
                'exists:regions,id',
            ],
            'region_divisions.*.start_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'region_divisions.*.end_station_id' => [
                'required',
                'exists:stations,id',
            ],
        ];
    }
}
