<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditTrainRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'train_name_id' => [
                'required',
                'exists:train_names,id',
            ],
            'driver_id'  => [
                'required',
                'numeric',
                'exists:users,id',
            ],
            'service_order' => [
                'required',
                Rule::unique(table: 'trains', column: 'service_order')
                    ->ignore(id: $this->train ? $this->train->id : ''),
            ],
            'locomotive_number_id' => [
                'required',
                'numeric',
                'exists:locomotive_numbers,id',
            ],
            'tail_number' => [
                'required',
                'string',
            ],
            'number_of_wagons' => [
                'required',
                'numeric',
            ],
            'line_id' => [
                'required',
                'exists:lines,id',
            ],
            'origin_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'destination_station_id' => [
                'required',
                'exists:stations,id',
            ],
            'date' => [
                'required',
                'date',
            ],
            'time' => [
                'required',
                'date_format:H:i',
            ],
            'tonnages' => [
                'required',
                'numeric',
            ],
        ];
    }
}
