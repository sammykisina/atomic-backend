<?php

declare(strict_types=1);

namespace Domains\ChiefCivilEngineer\Requests;

use Domains\ChiefCivilEngineer\Enums\RegionAssignmentTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegionUserRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'region_id' => [
                'required',
                'exists:regions,id',
            ],
            'line_id' => [
                'required',
                'exists:lines,id',
            ],

            'start_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'end_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'type' => [
                'required',
                Rule::enum(type: RegionAssignmentTypes::class),
            ],
        ];
    }
}
