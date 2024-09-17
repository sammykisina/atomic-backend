<?php

declare(strict_types=1);

namespace Domains\Driver\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditJourneyRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'train' => [
                'required',
                'string',
            ],
            'service_order' => [
                'required',
                Rule::unique(table: 'journeys', column: 'service_order')->ignore(id: $this->journey ? $this->journey->id : ''),
            ],
            'number_of_coaches' => [
                'required',
                'numeric',
            ],
            'origin_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'destination_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'current_location_latitude' => [
                'required',
                'numeric',
            ],
            'current_location_longitude' => [
                'required',
                'numeric',
            ],
        ];
    }
}
