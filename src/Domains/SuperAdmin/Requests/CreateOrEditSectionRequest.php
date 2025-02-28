<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use App\Enums\Superadmin\SectionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditSectionRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'start_name' => [
                'required',
                'string',
            ],
            'end_name' => [
                'required',
                'string',
            ],
            'start_kilometer' => [
                'numeric',
                'required',
            ],
            'end_kilometer' => [
                'numeric',
                'required',
            ] ,

            'start_latitude' => [
                'numeric',
                'required',
            ] ,
            'start_longitude' => [
                'numeric',
                'required',
            ],
            'end_latitude' => [
                'numeric',
                'required',
            ] ,
            'end_longitude' => [
                'numeric',
                'required',
            ],
            'number_of_kilometers_to_divide_section_to_subsection' => [
                'numeric',
                'required',
            ],
            'line_id' => [
                'required',
                'exists:lines,id',
            ],
            'station_id' => [
                'nullable',
                'exists:stations,id',
            ],
            'has_trip_line' => [
                'boolean',
            ],
            'section_type' => [
                'required',
                Rule::enum(type: SectionType::class),
            ],
        ];
    }
}
