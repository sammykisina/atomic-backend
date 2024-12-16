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
            'type' => [
                'required',
                'in:STATION,LOOP,SECTION',
            ],
            'area_id' => [
                'required',
                'integer',
            ],
            'distance_remaining' => [
                'required',
                'numeric',
            ],
            'latitude' => [
                'required',
            ],
            'longitude' => [
                'required',
            ],
        ];
    }
}
