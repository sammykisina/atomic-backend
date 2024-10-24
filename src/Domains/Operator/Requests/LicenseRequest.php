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
            'origin' => ['required', 'array'],
            'origin.origin_id' => ['required', 'integer'],
            'origin.type' => ['required', 'in:STATION,LOOP,SECTION'],

            'through' => ['array'],
            'through.*.id' => ['integer'],
            'through.*.type' => ['in:STATION,LOOP,SECTION'],

            'destination' => ['required', 'array'],
            'destination.destination_id' => ['required', 'integer'],
            'destination.type' => ['required', 'in:STATION,LOOP,SECTION'],
        ];
    }
}
