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
            'path' => [
                'required',
                'array',
            ],

            'path.*.origin_station_id' => [
                'required',
                'exists:stations,id',
            ],

            'path.*.originate_from_main_line' => [
                'required',
                'boolean',
            ],
            'path.*.origin_loop_id' => [
                'required_if:originate_from_main_line,false',
                'exists:loops,id',
            ],

            'path.*.section_id' => [
                'nullable',
                'exists:sections,id',
            ],

            'path.*.destination_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'path.*.stop_at_main_line' => [
                'required',
                'boolean',
            ],
            'path.*.destination_loop_id' => [
                'required_if:stop_at_main_line,false',
                'exists:loops,id',
            ],
        ];
    }
}
