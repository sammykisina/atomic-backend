<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Domains\SuperAdmin\Enums\LoopPositions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditLoopRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>  */
    public function rules(): array
    {
        return [
            'distance' => [
                'required',
                'numeric',
            ],

            'start_latitude_top' => [
                'numeric',
                'required',
            ] ,
            'start_longitude_top' => [
                'numeric',
                'required',
            ],
            'start_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            'start_longitude_bottom' => [
                'numeric',
                'required',
            ],
            'end_latitude_top' => [
                'numeric',
                'required',
            ] ,
            'end_longitude_top' => [
                'numeric',
                'required',
            ],
            'end_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            'end_longitude_bottom' => [
                'numeric',
                'required',
            ],

            'position' => [
                'required',
                Rule::enum(LoopPositions::class),
                Rule::unique(
                    table: 'loops',
                    column: 'position',
                )->where(
                    column: 'line_id',
                    value: $this->line->id,
                )->where(
                    column: 'station_id',
                    value: $this->station->id,
                )->ignore($this->loop ? $this->loop->id : ''),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'position' => 'This loop already exists.',
        ];
    }
}
