<?php

declare(strict_types=1);

namespace Domains\Operator\Requests;

use Domains\Operator\Enums\LicenseTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LicenseRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'origin' => ['required', 'array'],
            'origin.origin_id' => ['required', 'integer'],
            'origin.type' => ['required', 'in:STATION,LOOP,SECTION'],
            'origin.coordinates' => ['array','required'],

            'through' => ['array'],
            'through.*.id' => ['integer'],
            'through.*.type' => ['in:STATION,LOOP,SECTION'],
            'through.*.coordinates' => ['array','required'],

            'destination' => ['required', 'array'],
            'destination.destination_id' => ['required', 'integer'],
            'destination.type' => ['required', 'in:STATION,LOOP,SECTION'],
            'destination.coordinates' => ['array','required'],

            'type' => [
                Rule::enum(type: LicenseTypes::class),
            ],
            'reason_for_sos_license' => [
                'required_if:type,SOS',
            ],
            'journey_to_be_rescued' => [
                'required_if:type,SOS',
            ],
        ];
    }
}
