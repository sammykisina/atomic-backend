<?php

declare(strict_types=1);

namespace Domains\Operator\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class LicenseRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'origin_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'destination_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'section_id' => [
                'required',
                'exists:sections,id',
            ],
            'stop_at_main_line' => [
                'required',
                'boolean',
            ],
            'loop_id' => [
                'required_if:stop_at_main_line,false',
                'exists:loops,id',
            ],
        ];
    }
}
