<?php

declare(strict_types=1);

namespace Domains\Driver\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class LocationRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'station_id' => [
                'nullable',
                'exists:stations,id',
            ],
            'main_id' => [
                'nullable',
                'exists:stations,id',
            ],
            'loop_id' => [
                'nullable',
                'exists:loops,id',
            ],
            'section_id' => [
                'nullable',
                'exists:loops,id',
            ],
        ];
    }
}
